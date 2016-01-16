<?php
/** サンプルプログラム : nlp_ai/training_code/code-4-5-3-e.php
  * [説明]
  * 「4.5.3.類似度を使って似ているテキストを探そう」の内容を扱うサンプルプログラム。
  * TF-IDF ベクトルを使い、類似したテキストを探す。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/* Corpus_20NewsGroups クラスを読み込む */
require_once( 'class/Corpus_20NewsGroups.php' );

/* Morpheme クラスを読み込む */
require_once( 'class/Morpheme.php' );

/* Vectorizer_TFIDF_English クラスを読み込む */
require_once( 'class/Vectorizer_TFIDF_English.php' );

/* Vector_Similarity クラスを読み込む */
require_once( 'class/Vector_Similarity.php' );


/** 
  * @var    object      Corpus_20NewsGroups クラスから生成したオブジェクト
  */
$c20ng      = new Corpus_20NewsGroups();

/** 
  * @var    object      Vectorizer_TFIDF_English クラスから生成したオブジェクト
  */
$ve         = new Vectorizer_TFIDF_English();

/** 
  * @var    object      Vector_Similarity クラスから生成したオブジェクト
  */
$vs         = new Vector_Similarity();

if( is_object( $c20ng ) ){
    if( is_object( $ve ) ){
        if( is_object( $vs ) ){
            /** 【処理概要】
              * 複数のカテゴリから無作為にコーパスを取り出し、
              * TF-IDF ベクトルを使って類似したコーパスを探す。
              */
            
            /** 
              * @var    array       実験に使うコーパスファイルリスト
              */
            $corpusFileList = array();
            
            /* 訓練用コーパスのカテゴリ一覧 */
            $categoryList   = $c20ng->getTrainingCategoryList();
            
            /* 各カテゴリから100個ずつコーパスファイルを取り出す。 */
            foreach( $categoryList as $eachCategory )
            {
                /* 各カテゴリに含まれるコーパスファイルの一覧を取得する */
                $fileList       = $c20ng->getTrainingCorpusList( $eachCategory );
                shuffle( $fileList );
                
                /* 取り出したコーパス数を記録する */
                $setCount       = 0;
                
                foreach( $fileList as $eachFile )
                {
                    $corpusFileList[]   = $eachFile;
                    ++$setCount;
                    if( $setCount >= 100 ){
                        break;
                    }
                }
            }
            
            /* 無作為に並べ替える */
            shuffle( $corpusFileList );
            
            printf( "*******************************************************\n" );
            printf( "Vector_Similarity を使った類似テキストの探索\n" );
            printf( "使用コーパス       = 20 News Groups コーパス\n" );
            printf( "コーパスファイル数 = %d\n" , sizeof( $corpusFileList ) );
            printf( "*******************************************************\n" );
            
            /** 実験に使うコーパスファイル群における
              * Document Frequency を求める。
              */
            foreach( $corpusFileList as $eachFile )
            {
                printf( "文書 %s を投入\n" , $eachFile );
                $corpusBody     = $c20ng->getTrainingContent( $eachFile );
                $ve->importDocument( $corpusBody );
            }
            
            printf( "\n\n" );
            while( true )
            {
                /** 【処理概要】
                  * 無作為に文書を選択し、それと類似した文書を探す。
                  * 類似度の計算には TF-IDF ベクトルを利用する。
                  */
                  
                /* 文書を無作為に選択する */
                shuffle( $corpusFileList );
                $baseFile       = $corpusFileList[0];
                
                /* 無作為に選択した文書の内容を読み込む */
                $corpusBody     = $c20ng->getTrainingContent( $baseFile );
                
                /* TF-IDF ベクトルを取得する */
                $tfIdfBase      = $ve->vectorize( $corpusBody );
                
                printf( "*******************************************************\n" );
                printf( "類似テキストの探索\n" );
                printf( "基準となるコーパスファイル = %s\n" , $baseFile );
                printf( "*******************************************************\n" );
                printf( "[内容]\n" );
                printf( "%s\n\n" , $corpusBody );
                
                /* 最も高い類似度 */
                $bestSimilarity     = 0;
                
                /* 最も高い類似度だったコーパスファイル */
                $bestFile           = null;
                
                /* $corpusFileList に含まれるすべてのコーパスファイルに対して総当たり戦で類似度を比較する */
                
                printf( "類似しているコーパスファイルを探しています。\n" );
                foreach( $corpusFileList as $eachTargetFile )
                {
                    /* 基準となるコーパスファイルとは別のファイルを選ぶ */
                    if( $baseFile !== $eachTargetFile ){
                        /* 比較するコーパスの内容を読み込む */
                        $corpusBodyTarget     = $c20ng->getTrainingContent( $eachTargetFile );
                        
                        /* TF-IDF ベクトルを取得する */
                        $tfIdfTarget          = $ve->vectorize( $corpusBodyTarget );
                        
                        /* 類似度を計算する */
                        $eachSimilarity       = $vs->culcSimilarity( $tfIdfBase , $tfIdfTarget );
                        
                        if( $eachSimilarity > $bestSimilarity ){
                            $bestSimilarity   = $eachSimilarity;
                            $bestFile         = $eachTargetFile;
                        }
                    }
                }
                
                printf( "【最も類似しているコーパスファイル】\n" );
                printf( "コーパスファイル名     = %s\n" , $bestFile );
                printf( "類似度                 = %f\n" , $bestSimilarity );
                printf( "[内容]\n" );
                printf( "%s\n" , $c20ng->getTrainingContent( $bestFile ) );
                
                /** 
                  * @var    string      入力された文字
                  */
                $textInput  = null;
                
                printf( "\n他の文書もTF-IDFを計算しますか？(y/n):" );
                /* 標準入力を利用し、キーボードからの入力を受け付ける */
                $stdin      = fopen( 'php://stdin' , 'r' );
                $textInput  = fgets( $stdin );
                fclose( $stdin );
                $textInput  = rtrim( $textInput );
                
                if( strtolower( $textInput ) !== 'y' ){
                    break;
                }
            }
        }else{
            printf( "Vector_Similarity オブジェクトを作成できません。処理を中断します。\n" );
            exit();
        }
    }else{
        printf( "Vectorizer_TFIDF_English オブジェクトを作成できません。処理を中断します。\n" );
        exit();
    }
}else{
    printf( "Corpus_20NewsGroups オブジェクトを作成できません。処理を中断します。\n" );
    exit();
}


?>