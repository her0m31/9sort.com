<?php
/** サンプルプログラム : nlp_ai/training_code/class/BayesLearning_20NewsGroups.php
  * [説明]
  * 「20 News Groups」コーパス用のベイジアンフィルタクラス。
  * 機械学習処理機能、判定機能を持つ。
  * 
  * プログラムのエンコーディングはUTF-8。
  */


class BayesLearning_20NewsGroups
{
    /** 
      * @var    object      Corpus_20NewsGroups クラスから生成したオブジェクト
      */
    var $c20ng      = null;
    
    /** 
      * @var    object      Morpheme クラスから生成したオブジェクト
      */
    var $m          = null;
    
    /** 
      * @var    object      PDO拡張モジュールのオブジェクト
      */
    var $dbh        = null;
    
    /** 
      * @var    string      MySQLに接続するためのDSN
      */
    var $dsn        = 'mysql:host=localhost;dbname=nlp_ai';
    
    /** 
      * @var    string      PDO用接続ユーザ名
      */
    var $pdoUsername = 'root';
    
    /** 
      * @var    string      PDO用接続パスワード。インストール時に設定したパスワードをセットする。
      */
    var $pdoPassword = null;
    
    /** 
      * @var    array       分類辞書を格納する配列。文書分類時に使用する。
      */
    var $dictionary = array();
    
    /** 
      * @var    int         分類辞書を読み込んだフィルタID
      */
    var $loadedFilterId = null;
    
    /** コンストラクタ
      * 必要なオブジェクトの初期化をする。
      * @param          void
      * @return         this
      */
    public function __construct()
    {
        /* Corpus_20NewsGroups クラスから生成したオブジェクト */
        $this->c20ng      = new Corpus_20NewsGroups();
        
        /* Morpheme クラスから生成したオブジェクト */
        $this->m          = new Morpheme();
        return $this;
    }
    
    
    
    /** データベースに接続する。既に接続されている場合は何もしない。
      * @param          void        
      * @return         void
      */
    function connectDB()
    {
        if( !is_object( $this->dbh ) ){
            /* PDO オブジェクトの生成 */
            $this->dbh        = new PDO( $this->dsn, $this->pdoUsername , $this->pdoPassword );
            $this->dbh->query( 'SET NAMES utf8' );
        }
    }
    
    
    
    /** 「20 News Groups」コーパスのカテゴリを指定し、フィルタの識別名を返す
      * @param              string          category            機械学習するカテゴリ
      * @return             string          フィルタの識別名
      */
    function getFilterName( $category )
    {
        if( strlen( $category ) > 0 ){
            return sprintf( '20NewsGroup:%s' , $category );
        }else{
            return null;
        }
    }
    
    
    
    /** フィルタの登録処理
      * @param              void
      * @return             boolean     true:正常終了   false:異常発生
      */
    function registerFilters()
    {
        if( is_object( $this->c20ng ) ){
            /* データベースに接続する */
            $this->connectDB();
            
            /* 訓練用コーパスのカテゴリ一覧 */
            $categoryList   = $this->c20ng->getTrainingCategoryList();
            
            foreach( $categoryList as $eachCategory )
            {
                /* 各フィルタの識別名 */
                $eachFilterName     = $this->getFilterName( $eachCategory );
                
                /* フィルタが既に存在しているかチェックする */
                $checkSql           = sprintf( 'SELECT COUNT(*) AS FLAG_EXISTS FROM m_filter WHERE name="%s"',
                                               $eachFilterName );
                
                /* フィルタの有無をチェックするSQLを実行する */
                $sth                = $this->dbh->prepare( $checkSql );
                $sth->execute();
                $eachResultSet      = $sth->fetch();
                if( $eachResultSet['FLAG_EXISTS'] == 0 ){
                    /* フィルタが未登録の場合 */
                    $insertSql      = sprintf( 'INSERT INTO m_filter ( name , target_corpus_size , exclude_corpus_size , th_score ) VALUES( "%s" , 0 , 0 , 0 )',
                                               $eachFilterName );
                    $sth            = $this->dbh->prepare( $insertSql );
                    if( $sth->execute() ){
                        printf( "フィルタを新しく追加しました: %s\n" , $eachFilterName );
                    }
                }else{
                    printf( "すでに登録済みのフィルタです: %s\n" , $eachFilterName );
                }
            }
            return true;
        }else{
            /* $this->c20ng が初期化されていない場合 */
            return false;
        }
    }
    
    
    
    /** 「20 News Groups」コーパスのカテゴリを指定し、対応するフィルタIDを返す
      * @param              string          category            機械学習するカテゴリ
      * @return             int             フィルタID
      */
    function getFilterIDByCategory( $category )
    {
        if( strlen( $category ) > 0 ){
            /* データベースに接続する */
            $this->connectDB();
            
            /* 各フィルタの識別名 */
            $eachFilterName     = $this->getFilterName( $category );
                
            /* フィルタの基本情報を取得するためのSQL */
            $baseSql            = sprintf( 'SELECT * FROM m_filter WHERE name="%s"',
                                           $eachFilterName );
                                               
            /* フィルタの基本情報を取得するSQLを実行する */
            $sth                = $this->dbh->prepare( $baseSql );
            $sth->execute();
            $eachResultSet      = $sth->fetch();
            if( isset( $eachResultSet['id'] ) ){
                return $eachResultSet['id'];
            }else{
                /* 該当するフィルタが見つからない場合 */
                return null;
            }
        }else{
            /* カテゴリが指定されていない場合 */
            return null;
        }
    }
    
    
    
    /** 「20 News Groups」コーパスのカテゴリを指定し、対応するフィルタを返す
      * @param              string          category            機械学習するカテゴリ
      * @return             array           フィルタの基本情報
      */
    function getFilterByCategory( $category )
    {
        if( strlen( $category ) > 0 ){
            /* データベースに接続する */
            $this->connectDB();
            
            /* 各フィルタの識別名 */
            $eachFilterName     = $this->getFilterName( $category );
                
            /* フィルタの基本情報を取得するためのSQL */
            $baseSql            = sprintf( 'SELECT * FROM m_filter WHERE name="%s"',
                                           $eachFilterName );
                                               
            /* フィルタの基本情報を取得するSQLを実行する */
            $sth                = $this->dbh->prepare( $baseSql );
            $sth->execute();
            $eachResultSet      = $sth->fetch();
            if( isset( $eachResultSet['id'] ) ){
                return $eachResultSet;
            }else{
                /* 該当するフィルタが見つからない場合 */
                return null;
            }
        }else{
            /* カテゴリが指定されていない場合 */
            return null;
        }
    }
    
    
    
    /**「20 News Groups」コーパスのカテゴリを指定し、分類したいカテゴリのコーパスを機械学習する。
      * @param              string          category            機械学習するカテゴリ
      * @return             boolean         true:正常終了   false:異常発生
      */
    function learnTargetCategory( $category )
    {
        $filterId       = $this->getFilterIDByCategory( $category );
        if( is_numeric( $filterId ) ){
            /* 分類したいカテゴリに含まれるコーパスファイルの一覧を取得する */
            $fileList       = $this->c20ng->getTrainingCorpusList( $category );
            
            /* 単語の出現回数を記録する */
            $wordCountMap   = array();
            
            foreach( $fileList as $eachFile )
            {
                /* 形態素解析結果 */
                $corpusBody             = $this->c20ng->getTrainingContent( $eachFile );
                $eachMorphemeResult     = $this->m->parseEnglish( $corpusBody );
                
                /* コーパスに含まれる単語の一覧を記録する連想配列 */
                $eachWordList               = array();
                foreach( $eachMorphemeResult as $eachItem )
                {
                    $eachWord                   = $eachItem['word'];
                    $eachWordList[$eachWord]    = true;
                }
                
                /* 各コーパスに含まれる単語の出現回数を$wordCountMapに記録する。 */
                foreach( $eachWordList as $eachWord => $dummy )
                {
                    if( !isset( $wordCountMap[$eachWord] ) ){
                        $wordCountMap[$eachWord]    = 1;
                    }else{
                        $wordCountMap[$eachWord]++;
                    }
                }
            }
            
            /* 各単語の出現回数を書き込んでゆく */
            foreach( $wordCountMap as $eachWord => $eachWordCount )
            {
                /* 既に登録済みの単語かどうか判定する。 */
                $checkSql           = sprintf( 'SELECT COUNT(*) AS FLAG_EXISTS FROM t_word_count WHERE filter_id=%d AND corpus_type=0 AND word="%s"',
                                               $filterId ,
                                               $eachWord );
                $sth                = $this->dbh->prepare( $checkSql );
                $sth->execute();
                $eachResultSet      = $sth->fetch();
                if( $eachResultSet['FLAG_EXISTS'] == 0 ){
                    /* 単語がテーブル t_word_count に未登録のとき */
                    $insertSql      = sprintf( 'INSERT INTO t_word_count ( filter_id , corpus_type , word , word_count , last_learned_datetime ) VALUES( %d , 0 , "%s" , %d , NOW() )',
                                               $filterId ,
                                               $eachWord ,
                                               $eachWordCount );
                    $sth            = $this->dbh->prepare( $insertSql );
                    $sth->execute();
                }else{
                    /* 単語がテーブル t_word_count に登録済みの場合 */
                    $updateSql      = sprintf( 'UPDATE t_word_count SET word_count=word_count+%d , last_learned_datetime=NOW() WHERE filter_id=%d AND corpus_type=0 AND word="%s"',
                                               $eachWordCount ,
                                               $filterId ,
                                               $eachWord );
                    $sth            = $this->dbh->prepare( $updateSql );
                    $sth->execute();
                }
            }
            
            /* 分類すべきカテゴリに属するコーパス数を加算する。 */
            $updateSql      = sprintf( 'UPDATE m_filter SET target_corpus_size=target_corpus_size+%d , last_learned_datetime=NOW() WHERE id=%d',
                                       sizeof( $fileList ) ,
                                       $filterId  );
            $sth            = $this->dbh->prepare( $updateSql );
            $sth->execute();
            return true;
        }else{
            /* カテゴリの指定が不正な場合 */
            return false;
        }
    }
    
    
    
    /**「20 News Groups」コーパスのカテゴリを指定し、分類したいカテゴリ以外のコーパスを機械学習する。
      * @param              string          category            機械学習するカテゴリ
      * @return             boolean         true:正常終了   false:異常発生
      */
    function learnExcludeCategory( $category )
    {
        $filterId       = $this->getFilterIDByCategory( $category );
        if( is_numeric( $filterId ) ){
            /* 訓練用コーパスのカテゴリ一覧 */
            $categoryList   = $this->c20ng->getTrainingCategoryList();
            
            foreach( $categoryList as $eachExcludeCategory )
            {
                /* $category 以外のカテゴリであることを確かめる */
                if( $category !== $eachExcludeCategory ){
                    printf( "分類したいカテゴリ以外のコーパスの学習: %s\n" , $eachExcludeCategory );
                    
                    /* 分類したいカテゴリ以外に含まれるコーパスファイルの一覧を取得する */
                    $excludeFileList   = $this->c20ng->getTrainingCorpusList( $eachExcludeCategory );
                    
                    /* 単語の出現回数を記録する */
                    $wordCountMap   = array();
                    
                    foreach( $excludeFileList as $eachFile )
                    {
                        /* 形態素解析結果 */
                        $corpusBody             = $this->c20ng->getTrainingContent( $eachFile );
                        $eachMorphemeResult     = $this->m->parseEnglish( $corpusBody );
                        
                        /* コーパスに含まれる単語の一覧を記録する連想配列 */
                        $eachWordList               = array();
                        foreach( $eachMorphemeResult as $eachItem )
                        {
                            $eachWord                   = $eachItem['word'];
                            $eachWordList[$eachWord]    = true;
                        }
                        
                        /* 各コーパスに含まれる単語の出現回数を$wordCountMapに記録する。 */
                        foreach( $eachWordList as $eachWord => $dummy )
                        {
                            if( !isset( $wordCountMap[$eachWord] ) ){
                                $wordCountMap[$eachWord]    = 1;
                            }else{
                                $wordCountMap[$eachWord]++;
                            }
                        }
                    }
                    
                    /* 各単語の出現回数を書き込んでゆく */
                    foreach( $wordCountMap as $eachWord => $eachWordCount )
                    {
                        /* 既に登録済みの単語かどうか判定する。 */
                        $checkSql           = sprintf( 'SELECT COUNT(*) AS FLAG_EXISTS FROM t_word_count WHERE filter_id=%d AND corpus_type=1 AND word="%s"',
                                                       $filterId ,
                                                       $eachWord );
                        $sth                = $this->dbh->prepare( $checkSql );
                        $sth->execute();
                        $eachResultSet      = $sth->fetch();
                        if( $eachResultSet['FLAG_EXISTS'] == 0 ){
                            /* 単語がテーブル t_word_count に未登録のとき */
                            $insertSql      = sprintf( 'INSERT INTO t_word_count ( filter_id , corpus_type , word , word_count , last_learned_datetime ) VALUES( %d , 1 , "%s" , %d , NOW() )',
                                                       $filterId ,
                                                       $eachWord ,
                                                       $eachWordCount );
                            $sth            = $this->dbh->prepare( $insertSql );
                            $sth->execute();
                        }else{
                            /* 単語がテーブル t_word_count に登録済みの場合 */
                            $updateSql      = sprintf( 'UPDATE t_word_count SET word_count=word_count+%d , last_learned_datetime=NOW() WHERE filter_id=%d AND corpus_type=1 AND word="%s"',
                                                       $eachWordCount ,
                                                       $filterId ,
                                                       $eachWord );
                            $sth            = $this->dbh->prepare( $updateSql );
                            $sth->execute();
                        }
                    }
                    
                    /* 分類すべきカテゴリ以外に属するコーパス数を加算する。 */
                    $updateSql      = sprintf( 'UPDATE m_filter SET exclude_corpus_size=exclude_corpus_size+%d , last_learned_datetime=NOW() WHERE id=%d',
                                               sizeof( $excludeFileList ) ,
                                               $filterId  );
                    $sth            = $this->dbh->prepare( $updateSql );
                    $sth->execute();
                }
            }
            return true;
        }else{
            /* カテゴリが指定されていない場合 */
            return false;
        }
    }
    
    
    
    /** 「20 News Groups」コーパスのカテゴリを指定し、機械学習処理を実行する。
      * @param              string          category            機械学習するカテゴリ
      * @return             boolean         true:正常終了   false:異常発生
      */
    function learn( $category )
    {
        if( strlen( $category ) > 0 ){
            /* データベースに接続する */
            $this->connectDB();
            
            $this->learnTargetCategory( $category );
            $this->learnExcludeCategory( $category );
            return true;
        }else{
            /* カテゴリが指定されていない場合 */
            return false;
        }
    }
    
    
    
    /** フィルタIDとコーパスタイプを指定し、単語の出現回数を配列で返す。
      * @param          int         filter_id           フィルタID
      * @param          int         corpus_type         0:分類したいカテゴリのコーパス   1:分類したいカテゴリ以外のコーパス
      * @return         array       単語の出現頻度
      */
    function getWordCountList( $filter_id , $corpus_type )
    {
        if( is_numeric( $filter_id ) ){
            if( is_numeric( $corpus_type ) ){
                /* 単語の出現回数を記録する配列 */
                $wordCountMap   = array();
                
                /* 単語の出現回数を取得するSELECT文を作る。メモリを節約するため不要なカラムは結果に含めない。 */
                $readingSql = sprintf( 'SELECT word,word_count FROM t_word_count WHERE filter_id=%d AND corpus_type=%d',
                                       $filter_id , $corpus_type );
                $sth                = $this->dbh->prepare( $readingSql );
                $sth->execute();
                while( $eachResultSet = $sth->fetch() )
                {
                    $eachWord                   = $eachResultSet['word'];
                    $wordCountMap[$eachWord]    = $eachResultSet['word_count'];
                }
                return $wordCountMap;
            }else{
                return null;
            }
        }else{
            return null;
        }
    }
    
    
    
    /** カテゴリを指定し、メモリ上に分類辞書を読み込む。
      * @param          string          category            カテゴリ
      * @param          int             min_count           最小出現回数
      * @return         boolean         true:正常終了   false:異常発生
      */
    function loadDictionary( $category , $min_count = 2 )
    {
        if( strlen( $category ) > 0 ){
            /* フィルタ基本情報を取得する */
            $filter     = $this->getFilterByCategory( $category );
            
            if( isset( $filter['id'] ) ){
                if( is_numeric( $filter['id'] ) ){
                    /* 分類辞書を初期化する */
                    unset( $this->dictionary );
                    
                    /* 分類したいカテゴリにおける単語の出現回数 */
                    $targetWordCountMap     = $this->getWordCountList( $filter['id'] , 0 );
                    
                    /* 分類したいカテゴリ以外における単語の出現回数 */
                    $excludeWordCountMap    = $this->getWordCountList( $filter['id'] , 1 );
                    
                    /* 分類したいカテゴリの単語から分類辞書に登録する */
                    foreach( $targetWordCountMap as $eachWord => $eachWordCount )
                    {
                        /* 最低回数以上出現した単語を選ぶ */
                        if( $eachWordCount >= $min_count ){
                            /* 分類したいカテゴリにおける出現確率 */
                            $probabilityTarget      = $eachWordCount / $filter['target_corpus_size'];
                            
                            /* 分類したいカテゴリ以外における出現確率 */
                            $probabilityExclude     = 0;
                            if( isset( $excludeWordCountMap[$eachWord] ) ){
                                $probabilityExclude     = $excludeWordCountMap[$eachWord] / $filter['exclude_corpus_size'];
                            }
                            
                            /* 各単語のスコアを計算し、分類辞書に記録する */
                            $this->dictionary[$eachWord]    = $probabilityTarget / ( $probabilityTarget + $probabilityExclude );
                        }
                    }
                    
                    /* 分類したいカテゴリ以外の単語を分類辞書に登録する */
                    foreach( $excludeWordCountMap as $eachWord => $eachWordCount )
                    {
                        /* 最低回数以上出現した単語を選ぶ */
                        if( $eachWordCount >= $min_count ){
                            /* 辞書に未登録の単語のみ処理を続行する */
                            if( !isset( $this->dictionary[$eachWord] ) ){
                                /* 分類したいカテゴリにおける出現確率 */
                                $probabilityTarget      = 0;
                                if( isset( $targetWordCountMap[$eachWord] ) ){
                                    $probabilityTarget      = $targetWordCountMap[$eachWord] / $filter['target_corpus_size'];
                                }
                                
                                /* 分類したいカテゴリ以外における出現確率 */
                                $probabilityExclude     = $eachWordCount / $filter['exclude_corpus_size'];
                                
                                /* 各単語のスコアを計算し、分類辞書に記録する */
                                $this->dictionary[$eachWord]    = $probabilityTarget / ( $probabilityTarget + $probabilityExclude );
                            }
                        }
                    }
                    return true;
                }else{
                    /* フィルタIDが不正な場合 */
                    return false;
                }
            }else{
                /* フィルタIDがセットされていない場合 */
                return false;
            }
        }else{
            /* カテゴリが指定されていない場合 */
            return false;
        }
    }
    
    
    
    /** 英語のテキストを指定し、その判定用スコアを計算する。
      * @param          string          text            判定用スコアを計算する英語のテキスト
      * @param          int             range           判定用スコアの計算に組み入れる単語の数。単語スコア上位から range 個の単語を計算に組み入れる。
      * @return         double          判定用スコア
      */
    function culcDocumentScore( $text , $range = 15 )
    {
        if( strlen( $text ) > 0 ){
            /* 形態素解析結果 */
            $eachMorphemeResult     = $this->m->parseEnglish( $text );
            
            /* 各単語のスコアを記録する配列 */
            $wordScoreMap           = array();
            
            foreach( $eachMorphemeResult as $eachItem )
            {
                $eachWord                   = $eachItem['word'];
                if( isset( $this->dictionary[$eachWord] ) ){
                    $wordScoreMap[$eachWord]    = $this->dictionary[$eachWord];
                }
            }
            
            /* 上位から $range 個の単語を採用する */
            arsort( $wordScoreMap );
            
            /* 上位から $range 個の単語のスコアの合計値 */
            $scoreSum       = 0;
            
            /* 判定用スコアに組み入れた単語数 */
            $culcCount      = 0;
            foreach( $wordScoreMap as $eachWord => $eachScore )
            {
                $scoreSum   += $eachScore;
                
                ++$culcCount;
                if( $culcCount >= $range ){
                    break;
                }
            }
            
            /* 文書の判定用スコア */
            $documentScore          = $scoreSum / $range;
            return $documentScore;
        }else{
            return null;
        }
    }
    
}

?>