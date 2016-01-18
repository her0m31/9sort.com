<?php
/** サンプルプログラム : nlp_ai/training_code/class/Vector_Similarity.php
  * [説明]
  * ２つのベクトル間の類似度をコサイン距離で求める。
  * 
  * プログラムのエンコーディングはUTF-8。
  */


class Vector_Similarity
{
    /** 連想配列でベクトルを渡し、絶対値を計算する。
      * @param          array           vector          ベクトルを表す連想配列
      * @return         double          ベクトルの絶対値
      */
    function culcAbs( $vector )
    {
        /* ベクトルの絶対値 */
        $vectorAbs  = 0;
        
        /* 途中経過計算用 */
        $vectorSum  = 0;
        
        foreach( $vector as $eachWord => $eachTfIdf )
        {
            $vectorSum  += $eachTfIdf * $eachTfIdf;
        }
        $vectorAbs  = sqrt( $vectorSum );
        return $vectorAbs;
    }
    
    
    
    /** 二つのベクトルを連想配列形式で指定し、内積を求める。
      * @param          array           vector_a            ベクトルＡ
      * @param          array           vector_b            ベクトルＢ
      * @return         double          内積
      */
    function culcProduct( $vector_a , $vector_b )
    {
        /* 全ての要素を列挙したものを格納する配列 */
        $elementList    = array();
        
        /* 内積の値 */
        $product        = 0;
        
        foreach( $vector_a as $eachWord => $eachValue )
        {
            $elementList[$eachWord] = true;
        }
        
        foreach( $vector_b as $eachWord => $eachValue )
        {
            $elementList[$eachWord] = true;
        }
        
        foreach( $elementList as $eachWord => $dummy )
        {
            $valueA     = 0;
            $valueB     = 0;
            if( isset( $vector_a[$eachWord] ) ){
                $valueA = $vector_a[$eachWord];
            }
            if( isset( $vector_b[$eachWord] ) ){
                $valueB = $vector_b[$eachWord];
            }
            $product    += $valueA * $valueB;
        }
        return $product;
    }
    
    
    
    /** 二つのベクトルを連想配列形式で指定し、類似度をコサイン距離で求める。
      * @param          array           vector_a            ベクトルＡ
      * @param          array           vector_b            ベクトルＢ
      * @return         double          類似度
      */
    function culcSimilarity( $vector_a , $vector_b )
    {
        /* 内積を求める */
        $product        = $this->culcProduct( $vector_a , $vector_b );
        
        /* 絶対値を求める */
        $absA           = $this->culcAbs( $vector_a );
        $absB           = $this->culcAbs( $vector_b );
        
        return $product / ($absA * $absB );
    }
}

?>