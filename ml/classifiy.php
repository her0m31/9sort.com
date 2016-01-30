<?php
/* Corpus クラスを読み込む */
require_once('class/Corpus.php');
/* Morpheme クラスを読み込む */
require_once('class/Morpheme.php');
/* BayesLearning クラスを読み込む */
require_once('class/BayesLearning.php');
/* @var object BayesLearning クラスから生成したオブジェクト */
$bayes = new BayesLearning();
/* MySQLに接続するためのパスワードを記入すること */
$bayes->pdoPassword = 'root';
/* @var object Corpus クラスから生成したオブジェクト */
$corpus = new Corpus();

$classifiyScores = array();
$corpusBody = null;

if(isset($argv[1]) === FALSE) {
  printf("引数を指定してください\n");
  exit();
}

if(is_object($corpus) === FALSE) {
  printf("Corpus オブジェクトを作成できません\n");
  exit();
}

if(is_object($bayes) === FALSE) {
  printf("BayesLearning オブジェクトを作成できません\n");
  exit();
}

$corpusBody = $corpus->getContent($argv[1]);

if(is_null($corpusBody)) {
  exit();
}

printf("***********************************************************\n");
printf("ベイジアンフィルタ : 文書の判定用スコアの計算\n");
printf("***********************************************************\n");

/* 各カテゴリの分類辞書をメモリ上に読み込み、無作為に選択したコーパスの判定用スコアを計算する */
$categoryList = $corpus->getTrainingCategoryList();

foreach($categoryList as $eachCategory) {
  printf("カテゴリ : %s\n", $eachCategory);

  /** 分類辞書をメモリ上に読み込む。
  * 判定用スコアを計算するので、単語の最低出現回数は2回に引き下げる（第2引数に2を指定）
  */
  $bayes->loadDictionary($eachCategory, 2);

  $classifiyScores = $bayes->culcDocumentScore($corpus->getContent($argv[1]));

  printf("%s\n", $classifiyScores);
  printf("\n\n");
}
?>
