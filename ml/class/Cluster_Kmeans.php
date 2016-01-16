<?php
/** サンプルプログラム : nlp_ai/training_code/class/Cluster_Kmeans.php
  * [説明]
  * K-means法を使い、ベクトル形式のデータをクラスタに分類する。
  * ベクトル間の距離の測定に Vector_Similarity クラスを使用する。
  * 本クラスを使用する際は、Vector_Similarity クラスを読み込んでおく必要がある。
  * プログラムのエンコーディングはUTF-8。
  */


class Cluster_Kmeans
{
    /** 
      * @var    array       クラスタリングするベクトルデータ群を格納する連想配列
      *                     キー = ベクトルの識別名（ファイル名やレコード番号等)
      *                     値   = TF-IDF ベクトルを表す連想配列
      */
    var $vectorCache    = array();
    
    
    /** 
      * @var    array       クラスタの重心を表すベクトルを格納する配列
      */
    var $centerVector   = array();
    
    
    /** 
      * @var    array       各クラスタに属するベクトルの識別名リストを格納する
      */
    var $clusterMember   = array();
    
    
    /** 
      * @var    int         クラスタの数。デフォルト値は3。
      */
    var $k              = 3;
    
    
    /** 
      * @var    object      Vector_Similarity クラスから生成したオブジェクト
      */
    var $vs             = null;
    
    
    /** 識別名とセットで、クラスタリング処理にかけるベクトルを登録する。
      * @param          string          vector_name         ベクトルの識別名（＝ファイル名、レコード番号など）
      * @param          array           vector              ベクトルを表す連想配列
      * @return         boolean         true:正常終了  false:異常発生
      */
    function addVector( $vector_name , $vector )
    {
        if( strlen( $vector_name ) > 0 ){
            $this->vectorCache[$vector_name]    = $vector;
            return true;
        }else{
            return false;
        }
    }
    
    
    
    /** 初期重心点を決定する。
      * なるべく離れたベクトルどうしを選ぶ。
      * @param          int         k           設定する重心の数、任意。
      * @return         boolean     true:正常終了   false:異常発生
      */
    function initCenter( $k = null )
    {
        if( isset( $k ) ){
            if( is_numeric( $k ) ){
                $this->k        = $k;
            }
        }
        
        /* 重心ベクトルの初期化 */
        $this->centerVector   = array();
        
        /** 【処理概要】
          * $this->centerVector に登録されている重心ベクトル群から見て、まんべんなく距離が離れているものを選ぶ。
          */
        for( $countK = 0 ; $countK < $this->k ; $countK++ )
        {
            /* 重心ベクトルが一つも決まっていないときは、無作為に重心ベクトルになるものを選ぶ */
            if( $countK == 0 ){
                $vectorNameList = array_keys( $this->vectorCache );
                shuffle( $vectorNameList );
                $vectorName             = $vectorNameList[0];
                printf( "初期重心ベクトルに採用: %s\n" , $vectorName );
                $this->centerVector     = array( $this->vectorCache[$vectorName] );
            }else{
                /* 最もまんべんなく距離が離れているベクトルの距離 */
                $lowestSimilarity       = $this->k;
                
                /* 最もまんべんなく距離が離れているベクトルの識別名 */
                $lowestVectorName       = null;
                
                foreach( $this->vectorCache as $eachVectorName => $eachVector )
                {
                    /* $eachVector と重心ベクトル間の距離の合計値 */
                    $eachSimilarity     = 0;
                    
                    foreach( $this->centerVector as $eachCenterVector )
                    {
                        $eachSimilarity += $this->culcSimilarity( $eachCenterVector , $eachVector );
                    }
                    
                    /* 最も距離が離れているものは、類似度が最も小さい */
                    if( $eachSimilarity < $lowestSimilarity ){
                        $lowestSimilarity   = $eachSimilarity;
                        $lowestVectorName   = $eachVectorName;
                    }
                }
                
                /* 最もまんべんなく離れているベクトルを重心に加える */
                printf( "初期重心ベクトルに採用: %s\n" , $lowestVectorName );
                printf( "平均類似度            : %f\n" , $lowestSimilarity / sizeof( $this->centerVector ) );
                $this->centerVector[]   = $this->vectorCache[$lowestVectorName];
            }
        }
        return true;
    }
    
    
    
    /** 二つのベクトルの間の類似度（コサイン距離）を測定する。
      * @param          array           vector_a        ベクトルＡ
      * @param          array           vector_b        ベクトルＢ
      * @return         double          類似度
      */
    function culcSimilarity( $vector_a , $vector_b )
    {
        if( !is_object( $this->vs ) ){
            $this->vs       = new Vector_Similarity();
        }
        return $this->vs->culcSimilarity( $vector_a , $vector_b );
    }
    
    
    
    /** 各ベクトルを最寄りの重心点に所属させる。
      * @param          void
      * @return         boolean         true:正常終了   false:異常発生
      */
    function applyCluster()
    {
        /* 各クラスタに属するベクトルの識別名リストを初期化する */
        $this->clusterMember    = array();
        
        foreach( $this->vectorCache as $eachVectorName => $eachVector )
        {
            /* 最寄りの重心との距離 */
            $bestSimilarity       = 0;
            
            /* 最寄りの重心番号 */
            $bestCenterNo         = null;
            
            foreach( $this->centerVector as $eachCenterNo => $eachCenterVector )
            {
                /* 各重心と $eachVector の距離 */
                $eachSimilarity     = $this->culcSimilarity( $eachCenterVector , $eachVector );
                
                /* 最寄りの重心は、最も類似度が高い */
                if( $eachSimilarity > $bestSimilarity ){
                    $bestSimilarity = $eachSimilarity;
                    $bestCenterNo   = $eachCenterNo;
                }
            }
            
            /* 最寄りの重心に所属する */
            if( !isset( $this->clusterMember[$bestCenterNo] ) ){
                $this->clusterMember[$bestCenterNo]     = array( $eachVectorName );
            }else{
                $this->clusterMember[$bestCenterNo][]   = $eachVectorName;
            }
        }
    }
    
    
    
    /** 重心を再計算し、重心の移動量を返す。
      * @param          void
      * @return         array       各重心の移動量(類似度の差分)
      */
    function reCulcCenter()
    {
        /* 各重心の移動量 */
        $centerMovement     = array();
        
        /* applyCluster() メソッドを呼び出した直後に呼び出すこと */
        foreach( $this->clusterMember as $eachCenterNo => $eachVectorList )
        {
            /* 再計算した重心のベクトル */
            $newCenter      = array();
            
            /* 各クラスタに属するベクトル数 */
            $clusterSize    = sizeof( $eachVectorList );
            
            foreach( $eachVectorList as $eachVectorName )
            {
                $eachVector     = $this->vectorCache[$eachVectorName];
                foreach( $eachVector as $eachElementName => $eachElementValue )
                {
                    if( !isset( $newCenter[$eachElementName] ) ){
                        $newCenter[$eachElementName]    = $eachElementValue / $clusterSize;
                    }else{
                        $newCenter[$eachElementName]   += $eachElementValue / $clusterSize;
                    }
                }
            }
            printf( "\n重心 %d を再計算しました。\n" , $eachCenterNo );
            printf( "含まれる次元数 = %d\n" , sizeof( $newCenter ) );
            
            /** 計算処理を高速化するため、次元数を減らす。
              * 値が大きい要素（＝特徴的な言葉）上位２０％を残す。
              */
            arsort( $newCenter );
            
            $centerSize     = sizeof( $newCenter );
            
            /* 加工後の重心点ベクトル */
            $cleanedCenter  = array();
            
            foreach( $newCenter as $eachElementName => $eachElementValue )
            {
                $cleanedCenter[$eachElementName]    = $eachElementValue;
                if( sizeof( $cleanedCenter ) > $centerSize * 0.20 ){
                    break;
                }
            }
            
            
            /** 新しい重心と古い重心の間の移動量を計算する。
              * 新旧重心ベクトルどうしの類似度を計算する。
              * 位置が近いほど、類似度は1.0に近づく。
              * よって、移動量は 1.0 - 類似度となる。
              */
            $centerSimilarity   = $this->culcSimilarity( $this->centerVector[$eachCenterNo] , $cleanedCenter );
            $centerMovement[]   = 1.0 - $centerSimilarity;
            printf( "重心 %d の移動量 : %f\n" , $eachCenterNo , 1.0 - $centerSimilarity );
            
            /* 新しい重心で更新する */
            $this->centerVector[$eachCenterNo]  = $cleanedCenter;
        }
        return $centerMovement;
    }
    
    
    
}

?>