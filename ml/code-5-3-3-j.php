<?php
/* 文書の判定用スコアを計算する */

/* Corpus クラスを読み込む */
require_once('class/Corpus.php');
/* Morpheme クラスを読み込む */
require_once('class/Morpheme.php');
/* BayesLearning クラスを読み込む */
require_once('class/BayesLearning.php');

/* @var object BayesLearning クラスから生成したオブジェクト */
$bayesKentei = new BayesLearning();
/* MySQLに接続するためのパスワードを記入すること */
$bayesKentei->pdoPassword = 'root';
/* @var object Corpus クラスから生成したオブジェクト */
$ckentei = new Corpus();

if(is_object($ckentei)) {
  if(is_object($bayesKentei)) {
    printf("***********************************************************\n");
    printf("ベイジアンフィルタ : 文書の判定用スコアの計算\n");
    printf("使用コーパス       : けんてーごっこ コーパス\n");
    printf("***********************************************************\n");

    /* 各カテゴリの分類辞書をメモリ上に読み込み、無作為に選択したコーパスの判定用スコアを計算する */
    $categoryList = $ckentei->getTrainingCategoryList();

    foreach($categoryList as $eachCategory) {
      printf("●カテゴリ : %s\n", $eachCategory);

      /** 分類辞書をメモリ上に読み込む。
      * 判定用スコアを計算するので、単語の最低出現回数は2回に引き下げる（第2引数に2を指定）
      */
      $bayesKentei->loadDictionary($eachCategory, 2);

      /* カテゴリに含まれるコーパスファイルの一覧を取得する */
      $fileList = $ckentei->getTrainingCorpusList($eachCategory);

      shuffle($fileList);

      /* 各カテゴリのコーパス10個を無作為に選択し、判定用スコアを計算し表示する */
      foreach($fileList as $eachPos => $eachFile) {
        if($eachPos < 10) {
          $corpusBody = $ckentei->getTrainingContent($eachFile);
          $documentScore = $bayesKentei->culcDocumentScore($corpusBody);
          printf("コーパス %s : 判定用スコア = %f\n", $eachFile, $documentScore);
        } else {
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
