<?php
/** サンプルプログラム : nlp_ai/training_code/class/Vectorizer_TFIDF_Japanese.php
  * [説明]
  * TF-IDFベクトルを生成するクラス。
  * 日本語の文書に形態素解析に対応する。
  * 
  * プログラムのエンコーディングはUTF-8。
  */


class Vectorizer_TFIDF_Japanese
{
    /** 
      * @var    array       各単語の出現文書数
      */
    var $documentCount      = array();
    
    
    /** 
      * @var    int         総文書数
      */
    var $totalDocumentCount = 0;
    
    
    /** 
      * @var    object      Morpheme クラスから生成したオブジェクト
      */
    var $m                  = null;
    
    
    
    /** 日本語の文書を指定し、各単語の出現文書数、総文書数を追加する。
      * @param          string          text            日本語の文書
      * @return         boolean         true:正常終了   false:異常発生
      */
    function importDocument( $text )
    {
        if( !isset( $this->m ) ){
            $this->m    = new Morpheme();
        }
        
        /* 形態素解析結果を取得する */
        $morphemeResult     = $this->m->parseJapanese( $text );
        
        /** 
          * @var    array       単語の有無を記録する連想配列
          */
        $wordMap            = array();
            
        /* 文書に含まれる単語を $wordMap に記録してゆく */
        foreach( $morphemeResult as $eachResult )
        {
            /* 単語の原形を使う */
            $eachWord       = $eachResult['info']['prototype'];
            
            /* $eachWord をキーとして、カウントを+1する */
            if( !isset( $wordMap[$eachWord] ) ){
                /* $wordMap に初めて登録するときは1をセットする */
                $wordMap[$eachWord] = 1;
            }else{
                $wordMap[$eachWord]++;
            }
        }
        
        /* 単語が含まれる文書数をカウントしてゆく */
        foreach( $wordMap as $eachWord => $eachCount )
        {
            if( !isset( $this->documentCount[$eachWord] ) ){
                /* $this->documentCount に初めて登録する単語の値には 1 をセットする */
                $this->documentCount[$eachWord]   = 1;
            }else{
                $this->documentCount[$eachWord]++;
            }
        }
        
        /* 総文書数を+1する */
        ++$this->totalDocumentCount;
        
        return true;
    }
    
    
    
    /** 日本語の文書を指定し、TF-IDF のベクトルを生成する
      * @param          string          text            日本語の文書
      * @return         array           TF-IDFベクトルを表す連想配列
      */
    function vectorize( $text )
    {
        if( !isset( $this->m ) ){
            $this->m    = new Morpheme();
        }
        
        /* 形態素解析結果を取得する */
        $morphemeResult     = $this->m->parseJapanese( $text );
        
        /** 
          * @var    array       単語の分布を記録する連想配列
          */
        $wordMap            = array();
        
        /* 文書に含まれる単語を $wordMap に記録してゆく */
        foreach( $morphemeResult as $eachResult )
        {
            /* 単語の原形を使う */
            $eachWord       = $eachResult['info']['prototype'];
            
            /* $eachWord をキーとして、カウントを+1する */
            if( !isset( $wordMap[$eachWord] ) ){
                /* $wordMap に初めて登録するときは1をセットする */
                $wordMap[$eachWord] = 1;
            }else{
                $wordMap[$eachWord]++;
            }
        }
        
        /** 
          * @var    array       各単語のTF-IDFの値を格納する
          */
        $tfIdf      = array();
        
        /* 文書を構成する単語の総数 */
        $totalWordCount = sizeof( $morphemeResult );
        
        foreach( $wordMap as $eachWord => $eachCount )
        {
            /* 無作為に選択した文書における単語 $eachWord の出現頻度 */
            $eachTF     = $eachCount / $totalWordCount;
            
            /* 文書グループ全体を通して $eachWord が出現する文書の出現頻度の逆数 */
            $eachIDF    = $this->totalDocumentCount / $this->documentCount[$eachWord];
            
            $eachTFIDF  = $eachTF * log( $eachIDF );
            
            $tfIdf[$eachWord]   = $eachTFIDF;
        }
        
        return $tfIdf;
    }
    
}

?>