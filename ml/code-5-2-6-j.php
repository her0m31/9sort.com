<?php
/* ベイジアンフィルタの機械学習処理を実施する */

/* Corpus クラスを読み込む */
require_once('class/Corpus.php');
/* Morpheme クラスを読み込む */
require_once('class/Morpheme.php');
/* BayesLearning クラスを読み込む */
require_once('class/BayesLearning.php');
/* @va object BayesLearning_Kentei クラスから生成したオブジェクト */
$bayesKentei = new BayesLearning();
/* MySQLに接続するためのパスワードを記入すること */
$bayesKentei->pdoPassword = 'root';
/* @var object Corpus クラスから生成したオブジェクト */
$ckentei = new Corpus();

if(is_object($ckentei)) {
  if(is_object($bayesKentei)) {
    printf("***********************************************************\n");
    printf("ベイジアンフィルタ : 単語スコアの確認\n");
    printf("使用コーパス       : けんてーごっこ コーパス\n");
    printf("***********************************************************\n");

    /* 各カテゴリの分類辞書をメモリ上に読み込み、スコアが大きい順に100個ずつ表示する */
    $categoryList = $ckentei->getTrainingCategoryList();

    foreach($categoryList as $eachCategory) {
      printf("●カテゴリ : %s\n", $eachCategory);

      /** 分類辞書をメモリ上に読み込む。
      * より特徴的な単語を絞り込むため、そのカテゴリで20回以上出現している単語に絞り込む（第2引数に20を指定）
      */
      $bayesKentei->loadDictionary($eachCategory, 20);

      /* スコアが大きい順にソートする */
      arsort($bayesKentei->dictionary);

      $showCount = 0;
      foreach($bayesKentei->dictionary as $eachWord => $eachScore) {
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
