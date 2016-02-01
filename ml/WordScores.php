<?php
/* 単語スコアの確認 */

/* Corpus クラスを読み込む */
require_once('class/Corpus.php');
/* Morpheme クラスを読み込む */
require_once('class/Morpheme.php');
/* BayesLearning クラスを読み込む */
require_once('class/BayesLearning.php');
/* @va object BayesLearning_Kentei クラスから生成したオブジェクト */
$bayes = new BayesLearning();
/* MySQLに接続するためのパスワードを記入すること */
$bayes->pdoPassword = 'root';
/* @var object Corpus クラスから生成したオブジェクト */
$corpus = new Corpus();

if(is_object($corpus)) {
  if(is_object($bayes)) {
    printf("***********************************************************\n");
    printf("ベイジアンフィルタ : 単語スコアの確認\n");
    printf("***********************************************************\n");

    /* 各カテゴリの分類辞書をメモリ上に読み込み、スコアが大きい順に100個ずつ表示する */
    $categoryList = $corpus->getTrainingCategoryList();

    foreach($categoryList as $eachCategory) {
      printf("●カテゴリ : %s\n", $eachCategory);

      /** 分類辞書をメモリ上に読み込む。
      * より特徴的な単語を絞り込むため、そのカテゴリで20回以上出現している単語に絞り込む（第2引数に20を指定）
      */
      $bayes->loadDictionary($eachCategory, 20);

      /* スコアが大きい順にソートする */
      arsort($bayes->dictionary);

      $showCount = 0;
      foreach($bayes->dictionary as $eachWord => $eachScore) {
        printf("単語 %s = %f\n", $eachWord, $eachScore);

        ++$showCount;
        if($showCount >= 100) {
          break;
        }
      }
      printf("\n\n");
    }
  } else {
    printf("BayesLearning オブジェクトを作成できません。処理を中断します。\n");
    exit();
  }
} else {
  printf("Corpus オブジェクトを作成できません。処理を中断します。\n");
  exit();
}
?>
