<?php
/** サンプルプログラム : nlp_ai/training_code/code-4-3-3-j.php
  * [説明]
  * 「4.3.3.文書のグループに登場する単語の頻度を求めよう：けんてーごっこコーパス編」の内容を扱うサンプルプログラム。
  * Corpus_Kentei と Morpheme クラスを使い、文書間における単語の出現頻度を計算する。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/* Corpus_Kentei クラスを読み込む */
require_once( 'class/Corpus_Kentei.php' );

/* Morpheme クラスを読み込む */
require_once( 'class/Morpheme.php' );

/** 
  * @var    object      Corpus_Kentei クラスから生成したオブジェクト
  */
$ckentei    = new Corpus_Kentei();

/** 
  * @var    object      Morpheme クラスから生成したオブジェクト
  */
$m      = new Morpheme();

/** 
  * @var    array       単語が出現する文書数を記録する
  */
$documentCount      = array();

if( is_object( $ckentei ) ){
    if( is_object( $m ) ){
        /** 【処理概要】
          * 無作為に選択したカテゴリに含まれる文書を分析し、文書間における単語の出現頻度を求める。
          * 
          * 単語の出現頻度　＝　単語が出現した文書数　÷　文書総数
          */
        
        /* 訓練用コーパスのカテゴリ一覧 */
        $categoryList   = $ckentei->getTrainingCategoryList();
        
        /* 表示するカテゴリを無作為に選択 */
        shuffle( $categoryList );
        $showCategory   = $categoryList[0];
        
        /* 指定したカテゴリに含まれるコーパスファイルの一覧を取得する */
        $fileList       = $ckentei->getTrainingCorpusList( $showCategory );
        
        /**
          * @var    int     文書総数
          */
        $totalDocumentCount = sizeof( $fileList );
        
        printf( "*******************************************************\n" );
        printf( "文書間における単語の出現頻度の計算\n" );
        printf( "使用コーパス = けんてーごっこ コーパス\n" );
        printf( "選択されたカテゴリ = %s\n" , $showCategory );
        printf( "*******************************************************\n" );
        
        /** $showCategory で指定されたカテゴリに含まれる各文書に対し、単語の有無をチェックし、
          * $documentCount に単語ごとの出現文書数をカウントしてゆく。
          */
        foreach( $fileList as $eachFile )
        {
            printf( "文書 %s を解析\n" , $eachFile );
            $corpusBody     = $ckentei->getTrainingContent( $eachFile );
            
            /* 形態素解析結果を取得する */
            $morphemeResult     = $m->parseJapanese( $corpusBody );
            
            /** 
              * @var    array       単語の有無を記録する連想配列
              */
            $wordMap            = array();
            
            /* 文書に含まれる単語を $wordMap に記録してゆく */
            foreach( $morphemeResult as $eachResult )
            {
                $eachWord       = $eachResult['word'];
                
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
                if( !isset( $documentCount[$eachWord] ) ){
                    /* $documentCount に初めて登録する単語の値には 1 をセットする */
                    $documentCount[$eachWord]   = 1;
                }else{
                    $documentCount[$eachWord]++;
                }
            }
        }
        
        printf( "*******************************************************\n" );
        printf( "各単語が出現した文書数\n" );
        printf( "*******************************************************\n" );
        $showCount  = 0;
        foreach( $documentCount as $eachWord => $eachCount )
        {
            printf( "[%d] 単語 %24s   = %f ( %d 文書 / %d 文書中 ) \n" ,
                    $showCount + 1 , 
                    $eachWord , 
                    $eachCount / $totalDocumentCount ,
                    $eachCount ,
                    $totalDocumentCount );
            ++$showCount;
            
            /* 100件表示したらループを終了する */
            if( $showCount >= 100 ){
                break;
            }
        }
        
    }else{
        printf( "Morpheme オブジェクトを作成できません。処理を中断します。\n" );
        exit();
    }
}else{
    printf( "Corpus_Kentei オブジェクトを作成できません。処理を中断します。\n" );
    exit();
}


?>