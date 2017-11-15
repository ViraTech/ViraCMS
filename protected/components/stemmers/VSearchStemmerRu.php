<?php
/**
 * ViraCMS English Stemmer (Porter Stemming Alorithm)
 * Adopted from http://forum.dklab.ru/php/advises/HeuristicWithoutTheDictionaryExtractionOfARootFromRussianWord.html
 * @author andyceo @url http://andyceo.ruware.com/
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSearchStemmerRu implements VSearchStemmerInterface
{
  var $VOWEL = '/аеиоуыэюя/u';
  var $PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/u';
  var $REFLEXIVE = '/(с[яь])$/u';
  var $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ных|ом|его|ого|еых|ую|юю|ая|яя|ою|ею)$/u';
  var $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/u';
  var $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/u';
  var $NOUN = '/(а|ев|ов|ие|ье|е|иями|ями|ами|ам|еи|ии|и|ией|ей|ой|ий|й|и|ы|ь|ию|ью|ю|ия|ья|я)$/u';
  var $RVRE = '/^(.*?[аеиоуыэюя])(.*)$/u';
  var $DERIVATIONAL = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/u';

  public function stem($word)
  {
    $stem = strtr($word, array(
      'ё' => 'е',
      'Ё' => 'Е',
    ));

    do {
      if (!preg_match($this->RVRE, $word, $p)) {
        break;
      }

      $start = $p[ 1 ];
      $RV = $p[ 2 ];

      if (!$RV) {
        break;
      }

      # Step 1
      if (!$this->s($RV, $this->PERFECTIVEGROUND, '')) {
        $this->s($RV, $this->REFLEXIVE, '');

        if ($this->s($RV, $this->ADJECTIVE, '')) {
          $this->s($RV, $this->PARTICIPLE, '');
        }
        else {
          if (!$this->s($RV, $this->VERB, '')) {
            $this->s($RV, $this->NOUN, '');
          }
        }
      }

      # Step 2
      $this->s($RV, '/и$/', '');

      # Step 3
      if ($this->m($RV, $this->DERIVATIONAL))
        $this->s($RV, '/ость?$/', '');

      # Step 4
      if (!$this->s($RV, '/ь$/', '')) {
        $this->s($RV, '/ейше?/', '');
        $this->s($RV, '/нн$/', 'н');
      }

      $stem = $start . $RV;
    } while (false);

    return $stem;
  }

  protected function s(&$s, $re, $to)
  {
    $orig = $s;
    $s = preg_replace($re, $to, $s);
    return $orig !== $s;
  }

  protected function m($s, $re)
  {
    return preg_match($re, $s);
  }
}
