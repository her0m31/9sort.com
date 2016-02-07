<?php
/* 形態素解析クラス 日本語と英語の文書の形態素解析に対応する */
class Morpheme {
  // @var object php_mecabオブジェクト
  var $mecab = null;

  /**
  * mecabを使って形態素解析する。
  * @param  string text 形態素解析をする文章(UTF-8)
  * @return array  形態素解析結果
  */
  function parseJapanese($text) {
    /** php_mecabオブジェクトが初期化されているかチェックする。
    * もし初期化されていなければ、その場で初期化する。
    */
    if(!is_object($this->mecab)) {
      $this->mecab = new MeCab_Tagger();
    }

    /* 形態素解析結果(戻り値用) */
    $morphemeList = array();
    /* mecabの形態素解析結果を1行単位に分割したもの */
    $resultSet = explode("\n", $this->mecab->parse($text));

    foreach($resultSet as $eachResult) {
      if(substr($eachResult, 0, 3) !== 'EOS' && !empty($eachResult) && !strstr($eachResult, "記号")) {
        /* 形態素と情報部分に分割する */
        list($eachMorpheme, $eachInfo) = explode("\t", $eachResult);
        /* 情報部分をリストに分割する */
        $infoColumns = explode(',', $eachInfo);

        /* よみがなが無い場合への対処*/
        if(sizeof($infoColumns) < 8) {
          $infoColumns[] = '';
          $infoColumns[] = '';
        }

        /* 形態素解析結果(戻り値用)に追加する */
        $morphemeList[] = array('word' => $eachMorpheme,
        'info' => array('type' => $infoColumns[0],
        'category_1'       => $infoColumns[1],
        'category_2'       => $infoColumns[2],
        'category_3'       => $infoColumns[3],
        'conjugation_type' => $infoColumns[4],
        'conjugation_form' => $infoColumns[5],
        'prototype'        => $infoColumns[6],
        'yomigana'         => $infoColumns[7],
        'yomigana_alt'     => $infoColumns[8]));
      }
    }

    return $morphemeList;
  }

  /** 単語の前後に付着した余計な文字を掃除する。
  * @param  string word
  * @return string 掃除した後の単語
  */
  function cleanWord($word) {
    while(true) {
      /* 掃除前の単語の文字列を記録 */
      $lengthBefore = strlen($word);
      $cleaned      = trim($word, ' .\"\'\`\_');

      /* trim()関数で掃除をしても変化がなければ、掃除終了と判定する */
      if(strlen($cleaned) == $lengthBefore) {
        break;
      } else {
        $word = $cleaned;
      }
    }

    return $word;
  }

  /** 英語のテキストを単語に分割する。
  * @param string text 分割する英語のテキスト
  * @return array 単語に分割した結果
  */
  function parseEnglish($text) {
    /** @var string セパレータ文字列 */
    $separator = " ,!?\"\|-+*/={}[]()<>:;@&^~\n\r\t";
      /* @var array 形態素解析結果(戻り値用) */
      $morphemeList = array();

      /* 現在位置 */
      $pos = 0;

      /* 最大位置＝文字列数 */
      $maxPos = strlen($text);

      /* バッファ文字列 */
      $buffer = '';

      while($pos <= $maxPos) {
        $checkByte = substr($text, $pos, 1);

        if(strpos($separator, $checkByte) !== false) {
          /* 区切り文字を検知した場合 */
          if(strlen($buffer) > 0) {
            $eachWord       = strtolower($buffer);
            $eachWord       = $this->cleanWord($eachWord);
            $morphemeList[] = array('word' => $eachWord);
            $buffer         = '';
          }
        } else {

          if(strlen($buffer) > 0) {
            $buffer .= $checkByte;
          } else {
            /* 単語候補の最初の文字を見つけた場合 */
            $buffer = $checkByte;
          }
        }

        ++$pos;
      }

      if(strlen($buffer) > 0) {
        $eachWord       = strtolower($buffer);
        $eachWord       = $this->cleanWord($eachWord);
        $morphemeList[] = array('word' => $eachWord);
      }

      return $morphemeList;
    }
  }
  ?>
