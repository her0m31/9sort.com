<?php
/* コーパスを簡便に取得するためのクラス */
class Corpus {
  /* @var string 訓練用コーパスのインストールディレクトリ */
  var $trainingRoot = '/home/vagrant/dev/9sort.com/ml/training';
  /* @var string 評価用コーパスのインストールディレクトリ */
  var $testRoot = '/home/vagrant/dev/9sort.com/ml/test';

  /** コーパスディレクトリに含まれるカテゴリディレクトリのリストを取得する。
  * コーパスディレクトリ直下にぶら下がっているサブディレクトリの名前がカテゴリ名に相当する。
  * @param string base_dir コーパスディレクトリ
  * @return array カテゴリリスト
  */
  function getCategoryList($base_dir) {
    $dh = @opendir($base_dir);
    if(is_resource($dh)) {
      /* @var array カテゴリリスト */
      $categoryList = array();

      while(($eachEntry = readdir($dh)) !== false) {
        if($eachEntry !== '.' && $eachEntry !== '..' && $eachEntry !== '.DS_Store') {
          $categoryList[] = $eachEntry;
        }
      }

      closedir($dh);
      return $categoryList;
    } else {
      printf("ディレクトリを読み込むことができません : %s\n", $base_dir);
    }
  }

  /** カテゴリディレクトリを指定し、コーパスファイルのパスのリストを取得する。
  * @param string corpus_root コーパスファイルのルートディレクトリ
  * @param string category_dir カテゴリディレクトリ
  * @return array ファイルのパスのリスト
  */
  function getFilePathList($corpus_root, $category_dir) {
    $dirPath = sprintf('%s/%s', $corpus_root, $category_dir);
    $dh = @opendir($dirPath);
    if(is_resource($dh)) {
      /* @var array コーパスファイルのパスのリスト */
      $filePathList = array();

      while(($eachEntry = readdir($dh)) !== false) {
        var_dump($eachEntry);

        if($eachEntry !== '.' && $eachEntry !== '..' && $eachEntry !== '.DS_Store') {
          $filePathList[] = sprintf('%s/%s', $category_dir, $eachEntry);
        }
      }

      closedir($dh);
      return $filePathList;
    } else {
      printf("ディレクトリを読み込むことができません : %s\n", $dirPath);
    }
  }

  /** 訓練用コーパスのカテゴリ一覧を取得する。
  * @param なし
  * @return array カテゴリ一覧
  */
  function getTrainingCategoryList() {
    return $this->getCategoryList($this->trainingRoot);
  }

  /** 評価用コーパスのカテゴリ一覧を取得する。
  * @param なし
  * @return array カテゴリ一覧
  */
  function getTestCategoryList() {
    return $this->getCategoryList($this->testRoot);
  }

  /** カテゴリを指定して、訓練用コーパスファイルの一覧を取得する。
  * @param string category カテゴリ
  * @return array コーパスファイルの一覧
  */
  function getTrainingCorpusList($category) {
    return $this->getFilePathList($this->trainingRoot, $category);
  }

  /** カテゴリを指定して、評価用コーパスファイルの一覧を取得する。
  * @param string category カテゴリ
  * @return array コーパスファイルの一覧
  */
  function getTestCorpusList($category) {
    return $this->getFilePathList($this->testRoot, $category);
  }

  /** コーパスのパスを指定し、内容を取得する。
  * @param string file_path 投稿メールテキストのパス
  * @return string 内容
  */
  function getContent($file_path) {
    if(is_file($file_path)) {
      $fp = @fopen($file_path, 'r');
      if(is_resource($fp)) {
        $content = fread($fp, filesize($file_path));
        fclose($fp);
        return $content;
      } else {
        printf("ファイルを開くことができません : %s\n", $file_path);
        return null;
      }
    } else {
      printf("ファイルが見つかりません : %s\n", $file_path);
      return null;
    }
  }

  /** パスを指定して訓練用コーパスの内容を取得する。
  * @param string path パス
  * @return string コーパスの内容
  */
  function getTrainingContent($path) {
    return $this->getContent(sprintf('%s/%s', $this->trainingRoot, $path));
  }

  /** パスを指定して評価用コーパスの内容を取得する。
  * @param string path パス
  * @return string コーパスの内容
  */
  function getTestContent($path) {
    return $this->getContent(sprintf('%s/%s', $this->testRoot, $path));
  }
}
?>
