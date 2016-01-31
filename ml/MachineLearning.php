<?php
/* ベイジアンフィルタの機械学習処理を実施する */
/* Corpus クラスを読み込む */
require_once('class/Corpus.php');
/* Morpheme クラスを読み込む */
require_once('class/Morpheme.php');
/* BayesLearning クラスを読み込む */
require_once('class/BayesLearning.php');
/* @var object BayesLearning クラスから生成したオブジェクト */
$bayes = new BayesLearning();
/* @var object Corpus クラスから生成したオブジェクト */
$courpus = new Corpus();
/* MySQLに接続するためのパスワードを記入すること */
$bayes->pdoPassword = 'root';

if(is_object($courpus) === FALSE) {
  printf("Corpus オブジェクトを作成できません.処理を中断します.\n");
  exit();
}

if(is_object($bayes) === FALSE) {
  printf("BayesLearning オブジェクトを作成できません.処理を中断します.\n");
  exit();
}

printf("**************************\n");
printf("ベイジアンフィルタ　  機械学習処理\n");
printf("**************************\n");

/* フィルタの登録処理 テーブルm_filterにフィルタを登録する */
/* フィルタ識別名を使いフィルタが登録済みかどうか判定する */
$bayes->registerFilters();
/* 分類したいカテゴリのコーパス、分類したいカテゴリ以外のコーパスの順に機械学習処理を進めてゆく */
$categoryList = $courpus->getTrainingCategoryList();
foreach($categoryList as $eachCategory) {
  printf("カテゴリ:%sを機械学習処理中\n", $eachCategory);
  $bayes->learn($eachCategory);
}
?>
