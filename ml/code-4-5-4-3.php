<?php
/** サンプルプログラム : nlp_ai/training_code/code-4-5-4-3.php
  * [説明]
  * 「4.5.4.3.K-Means法で「けんてーごっこ」コーパスを分類してみよう」の内容を扱うサンプルプログラム。
  * TF-IDF ベクトルを使い、類似したテキストを探す。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/* Corpus_Kentei クラスを読み込む */
require_once( 'class/Corpus_Kentei.php' );

/* Morpheme クラスを読み込む */
require_once( 'class/Morpheme.php' );

/* Vectorizer_TFIDF_Japanese クラスを読み込む */
require_once( 'class/Vectorizer_TFIDF_Japanese.php' );

/* Vector_Similarity クラスを読み込む */
require_once( 'class/Vector_Similarity.php' );

/* Cluster_Kmeans クラスを読み込む */
require_once( 'class/Cluster_Kmeans.php' );

/** 
  * @var    object      Corpus_Kentei クラスから生成したオブジェクト
  */
$ckentei    = new Corpus_Kentei();

/** 
  * @var    object      Vectorizer_TFIDF_Japanese クラスから生成したオブジェクト
  */
$vj         = new Vectorizer_TFIDF_Japanese();

/** 
  * @var    object      Vector_Similarity クラスから生成したオブジェクト
  */
$vs         = new Vector_Similarity();


/** 
  * @var    objecy      Cluster_Kmeans クラスから生成オブジェクト
  */
$ck         = new Cluster_Kmeans();

if( is_object( $ckentei ) ){
    if( is_object( $vj ) ){
        if( is_object( $vs ) ){
            if( is_object( $ck ) ){
                
                /** 
                  * @var    array       実験に使うコーパスファイルリスト
                  */
                $corpusFileList = array();
                
                /* 訓練用コーパスのカテゴリ一覧 */
                $categoryList   = $ckentei->getTrainingCategoryList();
                
                /* 各カテゴリから500個ずつコーパスファイルを取り出す。 */
                foreach( $categoryList as $eachPos => $eachCategory )
                {
                    /* 各カテゴリに含まれるコーパスファイルの一覧を取得する */
                    $fileList       = $ckentei->getTrainingCorpusList( $eachCategory );
                    shuffle( $fileList );
                    
                    foreach( $fileList as $eachFilePos => $eachFile )
                    {
                        /* 各カテゴリから取り出すコーパスファイルは500個までとする */
                        if( $eachFilePos >= 500 ){
                            break;
                        }
                        $corpusFileList[]   = $eachFile;
                    }
                }
                
                /* 無作為に並べ替える */
                shuffle( $corpusFileList );
                
                printf( "*******************************************************\n" );
                printf( "Vector_Similarity を使ったクラスタリング\n" );
                printf( "使用コーパス       = けんてーごっこ コーパス\n" );
                printf( "コーパスファイル数 = %d\n" , sizeof( $corpusFileList ) );
                printf( "*******************************************************\n" );
                
                /** 実験に使うコーパスファイル群における
                  * Document Frequency を求める。
                  */
                foreach( $corpusFileList as $eachFile )
                {
                    printf( "文書 %s を投入\n" , $eachFile );
                    $corpusBody     = $ckentei->getTrainingContent( $eachFile );
                    $vj->importDocument( $corpusBody );
                }
                
                /* ベクトルを Cluster_Kmeans オブジェクトに登録する */
                printf( "クラスタリングするベクトルを登録しています...\n" );
                foreach( $corpusFileList as $eachFile )
                {
                    /* コーパスの内容を読み込む */
                    $corpusBody     = $ckentei->getTrainingContent( $eachFile );
                    
                    /* TF-IDF ベクトルを取得する */
                    $tfIdfTarget    = $vj->vectorize( $corpusBody );
                    
                    /* TF-IDF ベクトルをクラスタリング対象として登録する */
                    $ck->addVector( $eachFile , $tfIdfTarget );
                }
                $ck->initCenter( 16 );
                
                printf( "\n\n" );
                while( true )
                {
                    /** 【処理概要】
                      * K-means法に基づいて、クラスタ形成処理をする。
                      */
                    
                    /* 各ベクトルを最寄りの重心に割り当てる */
                    $ck->applyCluster();
                    
                    /* 重心を再計算し、その移動量を取得する */
                    $centerMovement = $ck->reCulcCenter();
                    
                    /** 各クラスタを構成するカテゴリの比率を表示する。
                      * クラスタリングがうまくいけば、1つのカテゴリが各クラスタの大半を占める。
                      */
                    foreach( $ck->clusterMember as $eachClusterNo => $eachClusterMember )
                    {
                        /* $eachClusterMember には、コーパスファイル名がセットされている */
                        
                        /* カテゴリ毎のコーパス数 */
                        $countByCategory  = array();
                        
                        foreach( $eachClusterMember as $eachCorpusFile )
                        {
                            list( $eachCategory , $eachFile ) = explode( '/' , $eachCorpusFile );
                            if( !isset( $countByCategory[$eachCategory] ) ){
                                $countByCategory[$eachCategory] = 1;
                            }else{
                                $countByCategory[$eachCategory]++;
                            }
                        }
                        
                        /* 各クラスタの大きさ（＝ベクトル数） */
                        $clusterSize    = sizeof( $eachClusterMember );
                        
                        printf( "\n" );
                        printf( "***********************************************************\n" );
                        printf( "クラスタ番号     : %d\n" , $eachClusterNo );
                        printf( "クラスタの大きさ : %d\n" , $clusterSize );
                        printf( "***********************************************************\n" );
                        printf( "[各カテゴリ毎のベクトル数]\n" );
                        
                        /* 数が多い順にソートする */
                        arsort( $countByCategory );
                        
                        foreach( $countByCategory as $eachCategory => $eachCount )
                        {
                            printf( "カテゴリ = %s : %2.2f ％( %d / %d )\n" ,
                                    $eachCategory ,
                                    $eachCount / $clusterSize * 100 ,
                                    $eachCount ,
                                    $clusterSize );
                        }
                        printf( "\n" );
                    }
                    
                    /** 
                      * @var    string      入力された文字
                      */
                    $textInput  = null;
                    
                    printf( "\nクラスタリング処理を続けますか？(y/n):" );
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
                printf( "Cluster_Kmeans オブジェクトを作成できません。処理を中断します。\n" );
                exit();
            }
        }else{
            printf( "Vector_Similarity オブジェクトを作成できません。処理を中断します。\n" );
            exit();
        }
    }else{
        printf( "Vectorizer_TFIDF_Japanese オブジェクトを作成できません。処理を中断します。\n" );
        exit();
    }
}else{
    printf( "Corpus_Kentei オブジェクトを作成できません。処理を中断します。\n" );
    exit();
}

?>