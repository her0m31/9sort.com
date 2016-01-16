<?php
/** サンプルプログラム : nlp_ai/training_code/code-4-4-1-4.php
  * [説明]
  * 「4.4.1.4.クラスを使ってTF-IDF ベクトルを生成してみよう」の内容を扱うサンプルプログラム。
  * Vectorizer_TFIDF_English クラスを使い、TF-IDF ベクトルを求める。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/* Corpus_20NewsGroups クラスを読み込む */
require_once( 'class/Corpus_20NewsGroups.php' );

/* Morpheme クラスを読み込む */
require_once( 'class/Morpheme.php' );

/* Vectorizer_TFIDF_English クラスを読み込む */
require_once( 'class/Vectorizer_TFIDF_English.php' );


/** 
  * @var    object      Corpus_20NewsGroups クラスから生成したオブジェクト
  */
$c20ng      = new Corpus_20NewsGroups();

/** 
  * @var    object      Vectorizer_TFIDF_English クラスから生成したオブジェクト
  */
$ve         = new Vectorizer_TFIDF_English();

if( is_object( $c20ng ) ){
    if( is_object( $ve ) ){
        /** 【処理概要】
          * 無作為に選択したカテゴリに含まれる文書を分析し、TF-IDF ベクトルを生成する。
          */
        
        /* 訓練用コーパスのカテゴリ一覧 */
        $categoryList   = $c20ng->getTrainingCategoryList();
        
        /* カテゴリを無作為に選択 */
        shuffle( $categoryList );
        $showCategory   = $categoryList[0];
        
        /* 指定したカテゴリに含まれるコーパスファイルの一覧を取得する */
        $fileList       = $c20ng->getTrainingCorpusList( $showCategory );
        
        printf( "*******************************************************\n" );
        printf( "Vectorizer_TFIDF_English クラスを使ったTF-IDFベクトルの生成\n" );
        printf( "使用コーパス = 20 News Groups コーパス\n" );
        printf( "選択されたカテゴリ = %s\n" , $showCategory );
        printf( "*******************************************************\n" );
        
        /** $showCategory で指定されたカテゴリに含まれる各文書を投入し、
          * Document Frequency を求める。
          */
        foreach( $fileList as $eachFile )
        {
            printf( "文書 %s を投入\n" , $eachFile );
            $corpusBody     = $c20ng->getTrainingContent( $eachFile );
            $ve->importDocument( $corpusBody );
        }
        
        printf( "\n\n" );
        while( true )
        {
            /** 【処理概要】
              * 無作為に文書を選択し、文書中に含まれる各単語の「TF-IDF」の値を計算する。
              */
              
            /* 「TF-IDF」を計算する文書を無作為に選択する */
            shuffle( $fileList );
            $eachFile   = $fileList[0];
            
            /* 無作為に選択した文書の内容を読み込む */
            $corpusBody     = $c20ng->getTrainingContent( $eachFile );
            
            /* TF-IDF ベクトルを取得する */
            $tfIdf          = $ve->vectorize( $corpusBody );
            
            printf( "*******************************************************\n" );
            printf( "TF-IDF の表示\n" );
            printf( "コーパスファイル = %s\n" , $eachFile );
            printf( "*******************************************************\n" );
            
            /* TF-IDFの値が大きい順にソートする */
            arsort( $tfIdf );
            
            $showCount  = 0;
            foreach( $tfIdf as $eachWord => $eachTFIDF )
            {
                printf( "[%d] 単語 %24s   = %f \n" ,
                        $showCount + 1 , 
                        $eachWord , 
                        $eachTFIDF );
                ++$showCount;
                
                /* 100件表示したらループを終了する */
                if( $showCount >= 100 ){
                    break;
                }
            }
        
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
        printf( "Vectorizer_TFIDF_English オブジェクトを作成できません。処理を中断します。\n" );
        exit();
    }
}else{
    printf( "Corpus_20NewsGroups オブジェクトを作成できません。処理を中断します。\n" );
    exit();
}


?>