<?php
/* ベイジアンフィルタの機械学習処理を実施する */

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

/* @var object Corpus_Kentei クラスから生成したオブジェクト */
$ckentei = new Corpus();

if(is_object($ckentei)) {
  if(is_object($bayesKentei)) {
    printf("***********************************************************\n");
    printf("ベイジアンフィルタ : 機械学習処理\n" );
    printf("***********************************************************\n");

    /* フィルタの登録処理 テーブルm_filter にフィルタを登録する フィルタ識別名を使い、フィルタが登録済みかどうか判定する */
    $bayesKentei->registerFilters();

    /* 分類したいカテゴリのコーパス、分類したいカテゴリ以外のコーパスの順に機械学習処理を進めてゆく */
    $categoryList = $ckentei->getTrainingCategoryList();
    foreach($categoryList as $eachCategory) {
      printf("カテゴリ : %s を機械学習処理中\n", $eachCategory);
      $bayesKentei->learn($eachCategory);
    }
  } else {
    printf("BayesLearning オブジェクトを作成できません。処理を中断します。\n");
    exit();
  }
}else{
  printf("Corpus オブジェクトを作成できません。処理を中断します。\n");
  exit();
}
?>
