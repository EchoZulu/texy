<?php
 /**
 * Texy! universal text -> html converter
 * --------------------------------------
 *
 * This source file is subject to the GNU GPL license.
 *
 * @author     David Grudl aka -dgx- <dave@dgx.cz>
 * @link       http://texy.info/
 * @copyright  Copyright (c) 2004-2006 David Grudl
 * @license    GNU GENERAL PUBLIC LICENSE v2
 * @package    Texy
 * @category   Text
 * @version    1.2a for PHP5 ONLY $Revision: 51 $ $Date: 2007-02-12 15:31:22 +0100 (po, 12 II 2007) $
 */if(version_compare(PHP_VERSION,'5.0.0','<'))die('Texy!: too old version of PHP!');define('TEXY','Version 1.2a for PHP5 $Revision: 51 $');define('TEXY_DIR',dirname(__FILE__).'/');

define('TEXY_CHAR','A-Za-z\x86-\xff');define('TEXY_CHAR_UTF','A-Za-z\x86-\x{ffff}');define('TEXY_NEWLINE',"\n");define('TEXY_HASH',"\x15-\x1F");define('TEXY_HASH_SPACES',"\x15-\x18");define('TEXY_HASH_NC',"\x19\x1B-\x1F");define('TEXY_HASH_WC',"\x1A-\x1F");define('TEXY_PATTERN_LINK_REF','\[[^\[\]\*\n'.TEXY_HASH.']+\]');define('TEXY_PATTERN_LINK_IMAGE','\[\*[^\n'.TEXY_HASH.']+\*\]');define('TEXY_PATTERN_LINK_URL','(?:\[[^\]\n]+\]|(?!\[)[^\s'.TEXY_HASH.']*?[^:);,.!?\s'.TEXY_HASH.'])');define('TEXY_PATTERN_LINK','(?::('.TEXY_PATTERN_LINK_URL.'))');define('TEXY_PATTERN_LINK_N','(?::('.TEXY_PATTERN_LINK_URL.'|:))');define('TEXY_PATTERN_EMAIL','[a-z0-9.+_-]+@[a-z0-9.+_-]{2,}\.[a-z]{2,}');define('TEXY_PATTERN_MODIFIER','(?:\ *(?<= |^)\.(\([^\n\)]+\)|\[[^\n\]]+\]|\{[^\n\}]+\})(\([^\n\)]+\)|\[[^\n\]]+\]|\{[^\n\}]+\})??(\([^\n\)]+\)|\[[^\n\]]+\]|\{[^\n\}]+\})??)');define('TEXY_PATTERN_MODIFIER_H','(?:\ *(?<= |^)\.(\([^\n\)]+\)|\[[^\n\]]+\]|\{[^\n\}]+\}|(?:<>|>|=|<))(\([^\n\)]+\)|\[[^\n\]]+\]|\{[^\n\}]+\}|(?:<>|>|=|<))??(\([^\n\)]+\)|\[[^\n\]]+\]|\{[^\n\}]+\}|(?:<>|>|=|<))??(\([^\n\)]+\)|\[[^\n\]]+\]|\{[^\n\}]+\}|(?:<>|>|=|<))??)');define('TEXY_PATTERN_MODIFIER_HV','(?:\ *(?<= |^)\.(\([^\n\)]+\)|\[[^\n\]]+\]|\{[^\n\}]+\}|(?:<>|>|=|<)|(?:\^|\-|\_))(\([^\n\)]+\)|\[[^\n\]]+\]|\{[^\n\}]+\}|(?:<>|>|=|<)|(?:\^|\-|\_))??(\([^\n\)]+\)|\[[^\n\]]+\]|\{[^\n\}]+\}|(?:<>|>|=|<)|(?:\^|\-|\_))??(\([^\n\)]+\)|\[[^\n\]]+\]|\{[^\n\}]+\}|(?:<>|>|=|<)|(?:\^|\-|\_))??(\([^\n\)]+\)|\[[^\n\]]+\]|\{[^\n\}]+\}|(?:<>|>|=|<)|(?:\^|\-|\_))??)');define('TEXY_PATTERN_IMAGE','\[\*([^\n'.TEXY_HASH.']+)'.TEXY_PATTERN_MODIFIER.'? *(\*|>|<)\]'); 

class
TexyModifier{const
HALIGN_LEFT='left';const
HALIGN_RIGHT='right';const
HALIGN_CENTER='center';const
HALIGN_JUSTIFY='justify';const
VALIGN_TOP='top';const
VALIGN_MIDDLE='middle';const
VALIGN_BOTTOM='bottom';protected$texy;public$id;public$classes=array();public$unfilteredClasses=array();public$styles=array();public$unfilteredStyles=array();public$unfilteredAttrs=array();public$hAlign;public$vAlign;public$title;public
function
__construct($texy){$this->texy=$texy;}public
function
setProperties(){$classes='';$styles='';foreach(func_get_args()as$arg){if($arg=='')continue;$argX=trim(substr($arg,1,-1));switch($arg{0}){case'{':$styles.=$argX.';';break;case'(':$this->title=$argX;break;case'[':$classes.=' '.$argX;break;case'^':$this->vAlign=self::VALIGN_TOP;break;case'-':$this->vAlign=self::VALIGN_MIDDLE;break;case'_':$this->vAlign=self::VALIGN_BOTTOM;break;case'=':$this->hAlign=self::HALIGN_JUSTIFY;break;case'>':$this->hAlign=self::HALIGN_RIGHT;break;case'<':$this->hAlign=$arg=='<>'?self::HALIGN_CENTER:self::HALIGN_LEFT;break;}}$this->parseStyles($styles);$this->parseClasses($classes);if(isset($this->classes['id'])){$this->id=$this->classes['id'];unset($this->classes['id']);}}public
function
getAttrs($tag){if($this->texy->allowedTags===Texy::ALL)return$this->unfilteredAttrs;if(is_array($this->texy->allowedTags)&&isset($this->texy->allowedTags[$tag])){$allowedAttrs=$this->texy->allowedTags[$tag];if($allowedAttrs===Texy::ALL)return$this->unfilteredAttrs;if(is_array($allowedAttrs)&&count($allowedAttrs)){$attrs=$this->unfilteredAttrs;foreach($attrs
as$key=>$foo)if(!in_array($key,$allowedAttrs))unset($attrs[$key]);return$attrs;}}return
array();}public
function
clear(){$this->id=NULL;$this->classes=array();$this->unfilteredClasses=array();$this->styles=array();$this->unfilteredStyles=array();$this->unfilteredAttrs=array();$this->hAlign=NULL;$this->vAlign=NULL;$this->title=NULL;}public
function
copyFrom($modifier){$this->classes=$modifier->classes;$this->unfilteredClasses=$modifier->unfilteredClasses;$this->styles=$modifier->styles;$this->unfilteredStyles=$modifier->unfilteredStyles;$this->unfilteredAttrs=$modifier->unfilteredAttrs;$this->id=$modifier->id;$this->hAlign=$modifier->hAlign;$this->vAlign=$modifier->vAlign;$this->title=$modifier->title;}public
function
parseClasses($str){if($str==NULL)return;$tmp=is_array($this->texy->allowedClasses)?array_flip($this->texy->allowedClasses):array();foreach(explode(' ',str_replace('#',' #',$str))as$value){if($value==='')continue;if($value{0}=='#'){$this->unfilteredClasses['id']=substr($value,1);if($this->texy->allowedClasses===Texy::ALL||isset($tmp[$value]))$this->classes['id']=substr($value,1);}else{$this->unfilteredClasses[]=$value;if($this->texy->allowedClasses===Texy::ALL||isset($tmp[$value]))$this->classes[]=$value;}}}public
function
parseStyles($str){if($str==NULL)return;$tmp=is_array($this->texy->allowedStyles)?array_flip($this->texy->allowedStyles):array();foreach(explode(';',$str)as$value){$pair=explode(':',$value.':');$property=strtolower(trim($pair[0]));$value=trim($pair[1]);if($property=='')continue;if(isset(TexyHtml::$accepted_attrs[$property])){$this->unfilteredAttrs[$property]=$value;}else{$this->unfilteredStyles[$property]=$value;if($this->texy->allowedStyles===Texy::ALL||isset($tmp[$property]))$this->styles[$property]=$value;}}}function
__set($nm,$val){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}function
__get($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__unset($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__isset($nm){return
FALSE;}} 

class
TexyUrl{protected$texy;public$value;protected$flags;protected$root;protected$URL;const
ABSOLUTE=1;const
RELATIVE=2;const
EMAIL=4;const
IMAGE=8;public
function
__construct($texy){$this->texy=$texy;}public
function
set($value,$root='',$isImage=FALSE){$this->value=trim($value);if($root<>NULL)$root=rtrim($root,'/\\').'/';$this->root=$root;$this->URL=NULL;if(preg_match('#^'.TEXY_PATTERN_EMAIL.'$#i',$this->value))$this->flags=self::EMAIL;elseif(preg_match('#^(https?://|ftp://|www\.|ftp\.|/)#i',$this->value))$this->flags=self::ABSOLUTE|($isImage?self::IMAGE:0);else$this->flags=self::RELATIVE|($isImage?self::IMAGE:0);}public
function
isAbsolute(){return(bool)($this->flags&self::ABSOLUTE);}public
function
isEmail(){return(bool)($this->flags&self::EMAIL);}public
function
isImage(){return(bool)($this->flags&self::IMAGE);}public
function
copyFrom($obj){$this->value=$obj->value;$this->flags=$obj->flags;$this->URL=$obj->URL;$this->root=$obj->root;}public
function
asURL(){if($this->URL!==NULL)return$this->URL;if($this->value=='')return$this->URL=$this->value;if($this->flags&self::EMAIL){if($this->texy->obfuscateEmail){$this->URL='mai';$s='lto:'.$this->value;for($i=0;$i<strlen($s);$i++)$this->URL.='&#'.ord($s{$i}).';';}else{$this->URL='mailto:'.$this->value;}return$this->URL;}if($this->flags&self::ABSOLUTE){$lower=strtolower($this->value);if(substr($lower,0,4)=='www.'){return$this->URL='http://'.$this->value;}elseif(substr($lower,0,4)=='ftp.'){return$this->URL='ftp://'.$this->value;}return$this->URL=$this->value;}if($this->flags&self::RELATIVE){return$this->URL=$this->root.$this->value;}}public
function
asTextual(){if($this->flags&self::EMAIL){return$this->texy->obfuscateEmail?strtr($this->value,array('@'=>"&#160;(at)&#160;")):$this->value;}if($this->flags&self::ABSOLUTE){$URL=$this->value;$lower=strtolower($URL);if(substr($lower,0,4)=='www.')$URL='none://'.$URL;elseif(substr($lower,0,4)=='ftp.')$URL='none://'.$URL;$parts=@parse_url($URL);if($parts===FALSE)return$this->value;$res='';if(isset($parts['scheme'])&&$parts['scheme']!=='none')$res.=$parts['scheme'].'://';if(isset($parts['host']))$res.=$parts['host'];if(isset($parts['path']))$res.=(strlen($parts['path'])>16?('/...'.preg_replace('#^.*(.{0,12})$#U','$1',$parts['path'])):$parts['path']);if(isset($parts['query'])){$res.=strlen($parts['query'])>4?'?...':('?'.$parts['query']);}elseif(isset($parts['fragment'])){$res.=strlen($parts['fragment'])>4?'#...':('#'.$parts['fragment']);}return$res;}return$this->value;}function
__set($nm,$val){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}function
__get($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__unset($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__isset($nm){return
FALSE;}} 

abstract
class
TexyDomElement{const
CONTENT_NONE=1;const
CONTENT_TEXTUAL=2;const
CONTENT_BLOCK=3;public$texy;public$contentType=TexyDomElement::CONTENT_NONE;public$behaveAsOpening;public
function
__construct($texy){$this->texy=$texy;}abstract
public
function
toHtml();protected
function
broadcast(){$this->texy->DOM->elements[]=$this;}function
__set($nm,$val){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}function
__get($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__unset($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__isset($nm){return
FALSE;}}class
TexyHtmlElement
extends
TexyDomElement{public$modifier;public$tag;public
function
__construct($texy){$this->texy=$texy;$this->modifier=new
TexyModifier($texy);}protected
function
generateTags(&$tags){if($this->tag){$attrs=$this->modifier->getAttrs($this->tag);$attrs['id']=$this->modifier->id;if($this->modifier->title!==NULL)$attrs['title']=$this->modifier->title;$attrs['class']=$this->modifier->classes;$attrs['style']=$this->modifier->styles;if($this->modifier->hAlign)$attrs['style']['text-align']=$this->modifier->hAlign;if($this->modifier->vAlign)$attrs['style']['vertical-align']=$this->modifier->vAlign;$tags[$this->tag]=$attrs;}}protected
function
generateContent(){}public
function
toHtml(){$this->generateTags($tags);return
TexyHtml::openingTags($tags).$this->generateContent().TexyHtml::closingTags($tags);}protected
function
broadcast(){parent::broadcast();if($this->modifier->id)$this->texy->DOM->elementsById[$this->modifier->id]=$this;if($this->modifier->classes)foreach($this->modifier->classes
as$class)$this->texy->DOM->elementsByClass[$class][]=$this;}}class
TexyBlockElement
extends
TexyHtmlElement{protected$children=array();public
function
appendChild($child){$this->children[]=$child;$this->contentType=max($this->contentType,$child->contentType);}public
function
getChild($key){if(isset($this->children[$key]))return$this->children[$key];}protected
function
generateContent(){$html='';foreach($this->children
as$child)$html.=$child->toHtml();return$html;}public
function
parse($text){$parser=new
TexyBlockParser($this);$parser->parse($text);}protected
function
broadcast(){parent::broadcast();foreach($this->children
as$child)$child->broadcast();}}class
TexyTextualElement
extends
TexyBlockElement{public$content;protected$htmlSafe=FALSE;public
function
setContent($text,$isHtmlSafe=FALSE){$this->content=$text;$this->htmlSafe=$isHtmlSafe;}public
function
getContent(){return$this->content;}public
function
safeContent($onlyReturn=FALSE){$safeContent=$this->htmlSafe?$this->content:TexyHtml::htmlChars($this->content);if($onlyReturn)return$safeContent;else{$this->htmlSafe=TRUE;return$this->content=$safeContent;}}protected
function
generateContent(){$content=$this->safeContent(TRUE);if($this->children){$table=array();foreach($this->children
as$key=>$child){$child->behaveAsOpening=Texy::isHashOpening($key);$table[$key]=$child->toHtml();}return
strtr($content,$table);}return$content;}public
function
parse($text){$parser=new
TexyLineParser($this);$parser->parse($text);}protected
function
hashKey($contentType=NULL,$opening=NULL){$border=($contentType==self::CONTENT_NONE)?"\x19":"\x1A";return$border.($opening?"\x1F":"").strtr(base_convert(count($this->children),10,4),'0123',"\x1B\x1C\x1D\x1E").$border;}protected
function
isHashOpening($hash){return$hash{1}=="\x1F";}public
function
appendChild($child,$innerText=NULL){$this->contentType=max($this->contentType,$child->contentType);if($child
instanceof
TexyInlineTagElement){$keyOpen=$this->hashKey($child->contentType,TRUE);$keyClose=$this->hashKey($child->contentType,FALSE);$this->children[$keyOpen]=$child;$this->children[$keyClose]=$child;return$keyOpen.$innerText.$keyClose;}$key=$this->hashKey($child->contentType);$this->children[$key]=$child;return$key;}}class
TexyInlineTagElement
extends
TexyHtmlElement{private$closingTag;public
function
toHtml(){if($this->behaveAsOpening){$this->generateTags($tags);$this->closingTag=TexyHtml::closingTags($tags);return
TexyHtml::openingTags($tags);}else{return$this->closingTag;}}}class
TexyDom
extends
TexyBlockElement{public$elements;public$elementsById;public$elementsByClass;public
function
parse($text){$text=Texy::wash($text);$text=str_replace("\r\n",TEXY_NEWLINE,$text);$text=str_replace("\r",TEXY_NEWLINE,$text);$tabWidth=$this->texy->tabWidth;while(strpos($text,"\t")!==FALSE)$text=preg_replace_callback('#^(.*)\t#mU',create_function('$matches',"return \$matches[1] . str_repeat(' ', $tabWidth - strlen(\$matches[1]) % $tabWidth);"),$text);$commentChars=$this->texy->utf?"\xC2\xA7":"\xA7";$text=preg_replace('#'.$commentChars.'{2,}(?!'.$commentChars.').*('.$commentChars.'{2,}|$)(?!'.$commentChars.')#mU','',$text);$text=preg_replace("#[\t ]+$#m",'',$text);foreach($this->texy->getModules()as$module)$text=$module->preProcess($text);parent::parse($text);}public
function
toHtml(){$html=parent::toHtml();$obj=new
TexyHtmlWellForm();$html=$obj->process($html);foreach($this->texy->getModules()as$module)$html=$module->postProcess($html);$html=Texy::unfreezeSpaces($html);$html=TexyHtml::checkEntities($html);if(!defined('TEXY_NOTICE_SHOWED')){$html.="\n<!-- generated by Texy! -->";define('TEXY_NOTICE_SHOWED',TRUE);}return$html;}public
function
buildLists(){$this->elements=array();$this->elementsById=array();$this->elementsByClass=array();$this->broadcast();}}class
TexyDomLine
extends
TexyTextualElement{public$elements;public$elementsById;public$elementsByClass;public
function
parse($text){$text=Texy::wash($text);$text=rtrim(strtr($text,array("\n"=>' ',"\r"=>'')));parent::parse($text);}public
function
toHtml(){$html=parent::toHtml();$wf=new
TexyHtmlWellForm();$html=$wf->process($html);$html=Texy::unfreezeSpaces($html);$html=TexyHtml::checkEntities($html);return$html;}public
function
buildLists(){$this->elements=array();$this->elementsById=array();$this->elementsByClass=array();$this->broadcast();}} 

abstract
class
TexyModule{protected$texy;public$allowed=Texy::ALL;public
function
__construct($texy){$this->texy=$texy;$texy->registerModule($this);}public
function
init(){}public
function
preProcess($text){return$text;}public
function
postProcess($text){return$text;}public
function
linePostProcess($line){return$line;}function
__set($nm,$val){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}function
__get($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__unset($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__isset($nm){return
FALSE;}} 

abstract
class
TexyParser{public$element;public
function
__construct($element){$this->element=$element;}abstract
public
function
parse($text);function
__set($nm,$val){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}function
__get($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__unset($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__isset($nm){return
FALSE;}}class
TexyBlockParser
extends
TexyParser{private$text;private$offset;public
function
receiveNext($pattern,&$matches){$matches=NULL;$ok=preg_match($pattern.'Am',$this->text,$matches,PREG_OFFSET_CAPTURE,$this->offset);if($ok){$this->offset+=strlen($matches[0][0])+1;foreach($matches
as$key=>$value)$matches[$key]=$value[0];}return$ok;}public
function
moveBackward($linesCount=1){while(--$this->offset>0)if($this->text{$this->offset-1}==TEXY_NEWLINE)if(--$linesCount<1)break;$this->offset=max($this->offset,0);}public
function
parse($text){$texy=$this->element->texy;$this->text=$text;$this->offset=0;$pb=$texy->getBlockPatterns();$keys=array_keys($pb);$arrMatches=$arrPos=array();foreach($keys
as$key)$arrPos[$key]=-1;do{$minKey=-1;$minPos=strlen($text);if($this->offset>=$minPos)break;foreach($keys
as$index=>$key){if($arrPos[$key]===FALSE)continue;if($arrPos[$key]<$this->offset){$delta=($arrPos[$key]==-2)?1:0;if(preg_match($pb[$key]['pattern'],$text,$arrMatches[$key],PREG_OFFSET_CAPTURE,$this->offset+$delta)){$m=&$arrMatches[$key];$arrPos[$key]=$m[0][1];foreach($m
as$keyX=>$valueX)$m[$keyX]=$valueX[0];}else{unset($keys[$index]);continue;}}if($arrPos[$key]===$this->offset){$minKey=$key;break;}if($arrPos[$key]<$minPos){$minPos=$arrPos[$key];$minKey=$key;}}$next=($minKey==-1)?strlen($text):$arrPos[$minKey];if($next>$this->offset){$str=substr($text,$this->offset,$next-$this->offset);$this->offset=$next;call_user_func_array(array($texy->genericBlockModule,'processBlock'),array($this,$str));continue;}$px=$pb[$minKey];$matches=$arrMatches[$minKey];$this->offset=$arrPos[$minKey]+strlen($matches[0])+1;$ok=call_user_func_array($px['handler'],array($this,$matches,$px['user']));if($ok===FALSE||($this->offset<=$arrPos[$minKey])){$this->offset=$arrPos[$minKey];$arrPos[$minKey]=-2;continue;}$arrPos[$minKey]=-1;}while(1);}}class
TexyLineParser
extends
TexyParser{public
function
parse($text){$element=$this->element;$texy=$element->texy;$offset=0;$hashStrLen=0;$pl=$texy->getLinePatterns();$keys=array_keys($pl);$arrMatches=$arrPos=array();foreach($keys
as$key)$arrPos[$key]=-1;do{$minKey=-1;$minPos=strlen($text);foreach($keys
as$index=>$key){if($arrPos[$key]<$offset){$delta=($arrPos[$key]==-2)?1:0;if(preg_match($pl[$key]['pattern'],$text,$arrMatches[$key],PREG_OFFSET_CAPTURE,$offset+$delta)){$m=&$arrMatches[$key];if(!strlen($m[0][0]))continue;$arrPos[$key]=$m[0][1];foreach($m
as$keyx=>$value)$m[$keyx]=$value[0];}else{unset($keys[$index]);continue;}}if($arrPos[$key]==$offset){$minKey=$key;break;}if($arrPos[$key]<$minPos){$minPos=$arrPos[$key];$minKey=$key;}}if($minKey==-1)break;$px=$pl[$minKey];$offset=$arrPos[$minKey];$replacement=call_user_func_array($px['handler'],array($this,$arrMatches[$minKey],$px['user']));$len=strlen($arrMatches[$minKey][0]);$text=substr_replace($text,$replacement,$offset,$len);$delta=strlen($replacement)-$len;foreach($keys
as$key){if($arrPos[$key]<$offset+$len)$arrPos[$key]=-1;else$arrPos[$key]+=$delta;}$arrPos[$minKey]=-2;}while(1);$text=TexyHtml::htmlChars($text,FALSE,TRUE);foreach($texy->getModules()as$module)$text=$module->linePostProcess($text);$element->setContent($text,TRUE);if($element->contentType==TexyDomElement::CONTENT_NONE){$s=trim(preg_replace('#['.TEXY_HASH.']+#','',$text));if(strlen($s))$element->contentType=TexyDomElement::CONTENT_TEXTUAL;}}}class
TexyHtmlParser
extends
TexyParser{public
function
parse($text){$element=$this->element;$texy=$element->texy;preg_match_all($texy->translatePattern('#<(/?)([a-z][a-z0-9_:-]*)(|\s(?:[\sa-z0-9:-]|=\s*"[^":HASH:]*"|=\s*\'[^\':HASH:]*\'|=[^>:HASH:]*)*)(/?)>|<!--([^:HASH:]*?)-->#is'),$text,$matches,PREG_OFFSET_CAPTURE|PREG_SET_ORDER);foreach(array_reverse($matches)as$m){$offset=$m[0][1];foreach($m
as$key=>$val)$m[$key]=$val[0];$text=substr_replace($text,$texy->htmlModule->process($this,$m),$offset,strlen($m[0]));}$text=TexyHtml::htmlChars($text,FALSE,TRUE);$element->setContent($text,TRUE);$element->contentType==TexyDomElement::CONTENT_BLOCK;}} 

TexyHtml::$valid=array_merge(TexyHtml::$block,TexyHtml::$inline);class
TexyHtml{const
EMPTYTAG='/';static
public$block=array('address'=>1,'blockquote'=>1,'caption'=>1,'col'=>1,'colgroup'=>1,'dd'=>1,'div'=>1,'dl'=>1,'dt'=>1,'fieldset'=>1,'form'=>1,'h1'=>1,'h2'=>1,'h3'=>1,'h4'=>1,'h5'=>1,'h6'=>1,'hr'=>1,'iframe'=>1,'legend'=>1,'li'=>1,'object'=>1,'ol'=>1,'p'=>1,'param'=>1,'pre'=>1,'table'=>1,'tbody'=>1,'td'=>1,'tfoot'=>1,'th'=>1,'thead'=>1,'tr'=>1,'ul'=>1,);static
public$inline=array('a'=>1,'abbr'=>1,'acronym'=>1,'area'=>1,'b'=>1,'big'=>1,'br'=>1,'button'=>1,'cite'=>1,'code'=>1,'del'=>1,'dfn'=>1,'em'=>1,'i'=>1,'img'=>1,'input'=>1,'ins'=>1,'kbd'=>1,'label'=>1,'map'=>1,'noscript'=>1,'optgroup'=>1,'option'=>1,'q'=>1,'samp'=>1,'script'=>1,'select'=>1,'small'=>1,'span'=>1,'strong'=>1,'sub'=>1,'sup'=>1,'textarea'=>1,'tt'=>1,'var'=>1,);static
public$empty=array('img'=>1,'hr'=>1,'br'=>1,'input'=>1,'meta'=>1,'area'=>1,'base'=>1,'col'=>1,'link'=>1,'param'=>1,);static
public$accepted_attrs=array('abbr'=>1,'accesskey'=>1,'align'=>1,'alt'=>1,'archive'=>1,'axis'=>1,'bgcolor'=>1,'cellpadding'=>1,'cellspacing'=>1,'char'=>1,'charoff'=>1,'charset'=>1,'cite'=>1,'classid'=>1,'codebase'=>1,'codetype'=>1,'colspan'=>1,'compact'=>1,'coords'=>1,'data'=>1,'datetime'=>1,'declare'=>1,'dir'=>1,'face'=>1,'frame'=>1,'headers'=>1,'href'=>1,'hreflang'=>1,'hspace'=>1,'ismap'=>1,'lang'=>1,'longdesc'=>1,'name'=>1,'noshade'=>1,'nowrap'=>1,'onblur'=>1,'onclick'=>1,'ondblclick'=>1,'onkeydown'=>1,'onkeypress'=>1,'onkeyup'=>1,'onmousedown'=>1,'onmousemove'=>1,'onmouseout'=>1,'onmouseover'=>1,'onmouseup'=>1,'rel'=>1,'rev'=>1,'rowspan'=>1,'rules'=>1,'scope'=>1,'shape'=>1,'size'=>1,'span'=>1,'src'=>1,'standby'=>1,'start'=>1,'summary'=>1,'tabindex'=>1,'target'=>1,'title'=>1,'type'=>1,'usemap'=>1,'valign'=>1,'value'=>1,'vspace'=>1,);static
public$valid;static
public
function
htmlChars($s,$inQuotes=FALSE,$entity=FALSE){$s=htmlSpecialChars($s,$inQuotes?ENT_COMPAT:ENT_NOQUOTES);if($entity)return
preg_replace('~&amp;([a-zA-Z0-9]+|#x[0-9a-fA-F]+|#[0-9]+);~','&$1;',$s);else
return$s;}static
public
function
checkEntities($html){static$entity=array('&AElig;'=>'&#198;','&Aacute;'=>'&#193;','&Acirc;'=>'&#194;','&Agrave;'=>'&#192;','&Alpha;'=>'&#913;','&Aring;'=>'&#197;','&Atilde;'=>'&#195;','&Auml;'=>'&#196;','&Beta;'=>'&#914;','&Ccedil;'=>'&#199;','&Chi;'=>'&#935;','&Dagger;'=>'&#8225;','&Delta;'=>'&#916;','&ETH;'=>'&#208;','&Eacute;'=>'&#201;','&Ecirc;'=>'&#202;','&Egrave;'=>'&#200;','&Epsilon;'=>'&#917;','&Eta;'=>'&#919;','&Euml;'=>'&#203;','&Gamma;'=>'&#915;','&Iacute;'=>'&#205;','&Icirc;'=>'&#206;','&Igrave;'=>'&#204;','&Iota;'=>'&#921;','&Iuml;'=>'&#207;','&Kappa;'=>'&#922;','&Lambda;'=>'&#923;','&Mu;'=>'&#924;','&Ntilde;'=>'&#209;','&Nu;'=>'&#925;','&OElig;'=>'&#338;','&Oacute;'=>'&#211;','&Ocirc;'=>'&#212;','&Ograve;'=>'&#210;','&Omega;'=>'&#937;','&Omicron;'=>'&#927;','&Oslash;'=>'&#216;','&Otilde;'=>'&#213;','&Ouml;'=>'&#214;','&Phi;'=>'&#934;','&Pi;'=>'&#928;','&Prime;'=>'&#8243;','&Psi;'=>'&#936;','&Rho;'=>'&#929;','&Scaron;'=>'&#352;','&Sigma;'=>'&#931;','&THORN;'=>'&#222;','&Tau;'=>'&#932;','&Theta;'=>'&#920;','&Uacute;'=>'&#218;','&Ucirc;'=>'&#219;','&Ugrave;'=>'&#217;','&Upsilon;'=>'&#933;','&Uuml;'=>'&#220;','&Xi;'=>'&#926;','&Yacute;'=>'&#221;','&Yuml;'=>'&#376;','&Zeta;'=>'&#918;','&aacute;'=>'&#225;','&acirc;'=>'&#226;','&acute;'=>'&#180;','&aelig;'=>'&#230;','&agrave;'=>'&#224;','&alefsym;'=>'&#8501;','&alpha;'=>'&#945;','&amp;'=>'&#38;','&and;'=>'&#8743;','&ang;'=>'&#8736;','&apos;'=>'&#39;','&aring;'=>'&#229;','&asymp;'=>'&#8776;','&atilde;'=>'&#227;','&auml;'=>'&#228;','&bdquo;'=>'&#8222;','&beta;'=>'&#946;','&brvbar;'=>'&#166;','&bull;'=>'&#8226;','&cap;'=>'&#8745;','&ccedil;'=>'&#231;','&cedil;'=>'&#184;','&cent;'=>'&#162;','&chi;'=>'&#967;','&circ;'=>'&#710;','&clubs;'=>'&#9827;','&cong;'=>'&#8773;','&copy;'=>'&#169;','&crarr;'=>'&#8629;','&cup;'=>'&#8746;','&curren;'=>'&#164;','&dArr;'=>'&#8659;','&dagger;'=>'&#8224;','&darr;'=>'&#8595;','&deg;'=>'&#176;','&delta;'=>'&#948;','&diams;'=>'&#9830;','&divide;'=>'&#247;','&eacute;'=>'&#233;','&ecirc;'=>'&#234;','&egrave;'=>'&#232;','&empty;'=>'&#8709;','&emsp;'=>'&#8195;','&ensp;'=>'&#8194;','&epsilon;'=>'&#949;','&equiv;'=>'&#8801;','&eta;'=>'&#951;','&eth;'=>'&#240;','&euml;'=>'&#235;','&euro;'=>'&#8364;','&exist;'=>'&#8707;','&fnof;'=>'&#402;','&forall;'=>'&#8704;','&frac12;'=>'&#189;','&frac14;'=>'&#188;','&frac34;'=>'&#190;','&frasl;'=>'&#8260;','&gamma;'=>'&#947;','&ge;'=>'&#8805;','&gt;'=>'&#62;','&hArr;'=>'&#8660;','&harr;'=>'&#8596;','&hearts;'=>'&#9829;','&hellip;'=>'&#8230;','&iacute;'=>'&#237;','&icirc;'=>'&#238;','&iexcl;'=>'&#161;','&igrave;'=>'&#236;','&image;'=>'&#8465;','&infin;'=>'&#8734;','&int;'=>'&#8747;','&iota;'=>'&#953;','&iquest;'=>'&#191;','&isin;'=>'&#8712;','&iuml;'=>'&#239;','&kappa;'=>'&#954;','&lArr;'=>'&#8656;','&lambda;'=>'&#955;','&lang;'=>'&#9001;','&laquo;'=>'&#171;','&larr;'=>'&#8592;','&lceil;'=>'&#8968;','&ldquo;'=>'&#8220;','&le;'=>'&#8804;','&lfloor;'=>'&#8970;','&lowast;'=>'&#8727;','&loz;'=>'&#9674;','&lrm;'=>'&#8206;','&lsaquo;'=>'&#8249;','&lsquo;'=>'&#8216;','&lt;'=>'&#60;','&macr;'=>'&#175;','&mdash;'=>'&#8212;','&micro;'=>'&#181;','&middot;'=>'&#183;','&minus;'=>'&#8722;','&mu;'=>'&#956;','&nabla;'=>'&#8711;','&nbsp;'=>'&#160;','&ndash;'=>'&#8211;','&ne;'=>'&#8800;','&ni;'=>'&#8715;','&not;'=>'&#172;','&notin;'=>'&#8713;','&nsub;'=>'&#8836;','&ntilde;'=>'&#241;','&nu;'=>'&#957;','&oacute;'=>'&#243;','&ocirc;'=>'&#244;','&oelig;'=>'&#339;','&ograve;'=>'&#242;','&oline;'=>'&#8254;','&omega;'=>'&#969;','&omicron;'=>'&#959;','&oplus;'=>'&#8853;','&or;'=>'&#8744;','&ordf;'=>'&#170;','&ordm;'=>'&#186;','&oslash;'=>'&#248;','&otilde;'=>'&#245;','&otimes;'=>'&#8855;','&ouml;'=>'&#246;','&para;'=>'&#182;','&part;'=>'&#8706;','&permil;'=>'&#8240;','&perp;'=>'&#8869;','&phi;'=>'&#966;','&pi;'=>'&#960;','&piv;'=>'&#982;','&plusmn;'=>'&#177;','&pound;'=>'&#163;','&prime;'=>'&#8242;','&prod;'=>'&#8719;','&prop;'=>'&#8733;','&psi;'=>'&#968;','&quot;'=>'&#34;','&rArr;'=>'&#8658;','&radic;'=>'&#8730;','&rang;'=>'&#9002;','&raquo;'=>'&#187;','&rarr;'=>'&#8594;','&rceil;'=>'&#8969;','&rdquo;'=>'&#8221;','&real;'=>'&#8476;','&reg;'=>'&#174;','&rfloor;'=>'&#8971;','&rho;'=>'&#961;','&rlm;'=>'&#8207;','&rsaquo;'=>'&#8250;','&rsquo;'=>'&#8217;','&sbquo;'=>'&#8218;','&scaron;'=>'&#353;','&sdot;'=>'&#8901;','&sect;'=>'&#167;','&shy;'=>'&#173;','&sigma;'=>'&#963;','&sigmaf;'=>'&#962;','&sim;'=>'&#8764;','&spades;'=>'&#9824;','&sub;'=>'&#8834;','&sube;'=>'&#8838;','&sum;'=>'&#8721;','&sup1;'=>'&#185;','&sup2;'=>'&#178;','&sup3;'=>'&#179;','&sup;'=>'&#8835;','&supe;'=>'&#8839;','&szlig;'=>'&#223;','&tau;'=>'&#964;','&there4;'=>'&#8756;','&theta;'=>'&#952;','&thetasym;'=>'&#977;','&thinsp;'=>'&#8201;','&thorn;'=>'&#254;','&tilde;'=>'&#732;','&times;'=>'&#215;','&trade;'=>'&#8482;','&uArr;'=>'&#8657;','&uacute;'=>'&#250;','&uarr;'=>'&#8593;','&ucirc;'=>'&#251;','&ugrave;'=>'&#249;','&uml;'=>'&#168;','&upsih;'=>'&#978;','&upsilon;'=>'&#965;','&uuml;'=>'&#252;','&weierp;'=>'&#8472;','&xi;'=>'&#958;','&yacute;'=>'&#253;','&yen;'=>'&#165;','&yuml;'=>'&#255;','&zeta;'=>'&#950;','&zwj;'=>'&#8205;','&zwnj;'=>'&#8204;',);static$allowed=array('&#38;'=>'&amp;','&#34;'=>'&quot;','&#60;'=>'&lt;','&#62;'=>'&gt;');$html=str_replace(array_keys($entity),array_values($entity),$html);$html=preg_replace('#&([a-zA-Z0-9]+);#','&amp;$1;',$html);return
strtr($html,$allowed);}static
public
function
openingTags($tags){$result='';foreach((array)$tags
as$tag=>$attrs){if($tag==NULL)continue;$empty=isset(self::$empty[$tag])||isset($attrs[self::EMPTYTAG]);$attrStr='';if(is_array($attrs)){unset($attrs[self::EMPTYTAG]);foreach(array_change_key_case($attrs,CASE_LOWER)as$name=>$value){if(is_array($value)){if($name=='style'){$style=array();foreach(array_change_key_case($value,CASE_LOWER)as$keyS=>$valueS)if($keyS&&($valueS!=='')&&($valueS!==NULL))$style[]=$keyS.':'.$valueS;$value=implode(';',$style);}else$value=implode(' ',array_unique($value));if($value=='')continue;}if($value===NULL||$value===FALSE)continue;$value=trim($value);$attrStr.=' '.self::htmlChars($name).'="'.Texy::freezeSpaces(self::htmlChars($value,TRUE,TRUE)).'"';}}$result.='<'.$tag.$attrStr.($empty?' /':'').'>';}return$result;}static
public
function
closingTags($tags){$result='';foreach(array_reverse((array)$tags,TRUE)as$tag=>$attrs){if($tag=='')continue;if(isset(self::$empty[$tag])||isset($attrs[self::EMPTYTAG]))continue;$result.='</'.$tag.'>';}return$result;}function
__set($nm,$val){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}function
__get($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__unset($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__isset($nm){return
FALSE;}} 

class
TexyHtmlWellForm{private$tagUsed;private$tagStack;private$autoClose=array('tbody'=>array('thead'=>1,'tbody'=>1,'tfoot'=>1,'colgoup'=>1),'colgroup'=>array('thead'=>1,'tbody'=>1,'tfoot'=>1,'colgoup'=>1),'dd'=>array('dt'=>1,'dd'=>1),'dt'=>array('dt'=>1,'dd'=>1),'li'=>array('li'=>1),'option'=>array('option'=>1),'p'=>array('address'=>1,'applet'=>1,'blockquote'=>1,'center'=>1,'dir'=>1,'div'=>1,'dl'=>1,'fieldset'=>1,'form'=>1,'h1'=>1,'h2'=>1,'h3'=>1,'h4'=>1,'h5'=>1,'h6'=>1,'hr'=>1,'isindex'=>1,'menu'=>1,'object'=>1,'ol'=>1,'p'=>1,'pre'=>1,'table'=>1,'ul'=>1),'td'=>array('th'=>1,'td'=>1,'tr'=>1,'thead'=>1,'tbody'=>1,'tfoot'=>1,'colgoup'=>1),'tfoot'=>array('thead'=>1,'tbody'=>1,'tfoot'=>1,'colgoup'=>1),'th'=>array('th'=>1,'td'=>1,'tr'=>1,'thead'=>1,'tbody'=>1,'tfoot'=>1,'colgoup'=>1),'thead'=>array('thead'=>1,'tbody'=>1,'tfoot'=>1,'colgoup'=>1),'tr'=>array('tr'=>1,'thead'=>1,'tbody'=>1,'tfoot'=>1,'colgoup'=>1),);public
function
process($text){$this->tagStack=array();$this->tagUsed=array();$text=preg_replace_callback('#<(/?)([a-z_:][a-z0-9._:-]*)(|\s.*)(/?)>()#Uis',array($this,'cb'),$text);if($this->tagStack){$pair=end($this->tagStack);while($pair!==FALSE){$text.='</'.$pair['tag'].'>';$pair=prev($this->tagStack);}}return$text;}private
function
cb($matches){list(,$mClosing,$mTag,$mAttr,$mEmpty)=$matches;if(isset(TexyHtml::$empty[$mTag])||$mEmpty)return$mClosing?'':'<'.$mTag.$mAttr.'/>';if($mClosing){$pair=end($this->tagStack);$s='';$i=1;while($pair!==FALSE){$s.='</'.$pair['tag'].'>';if($pair['tag']==$mTag)break;$this->tagUsed[$pair['tag']]--;$pair=prev($this->tagStack);$i++;}if($pair===FALSE)return'';if(isset(TexyHtml::$block[$mTag])){array_splice($this->tagStack,-$i);return$s;}unset($this->tagStack[key($this->tagStack)]);$pair=current($this->tagStack);while($pair!==FALSE){$s.='<'.$pair['tag'].$pair['attr'].'>';@$this->tagUsed[$pair['tag']]++;$pair=next($this->tagStack);}return$s;}else{$s='';$pair=end($this->tagStack);while($pair&&isset($this->autoClose[$pair['tag']])&&isset($this->autoClose[$pair['tag']][$mTag])){$s.='</'.$pair['tag'].'>';$this->tagUsed[$pair['tag']]--;unset($this->tagStack[key($this->tagStack)]);$pair=end($this->tagStack);}$pair=array('attr'=>$mAttr,'tag'=>$mTag,);$this->tagStack[]=$pair;@$this->tagUsed[$pair['tag']]++;$s.='<'.$mTag.$mAttr.'>';return$s;}}function
__set($nm,$val){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}function
__get($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__unset($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__isset($nm){return
FALSE;}} 

class
TexyBlockModule
extends
TexyModule{public$allowed;public$codeHandler;public$divHandler;public$htmlHandler;public
function
__construct($texy){parent::__construct($texy);$this->allowed=(object)NULL;$this->allowed->pre=TRUE;$this->allowed->text=TRUE;$this->allowed->html=TRUE;$this->allowed->div=TRUE;$this->allowed->source=TRUE;$this->allowed->comment=TRUE;}public
function
init(){$this->texy->registerBlockPattern($this,'processBlock','#^/--+ *(?:(code|samp|text|html|div|notexy|source|comment)( .*)?|) *<MODIFIER_H>?\n(.*\n)?(?:\\\\--+ *\\1?|\z)()$#mUsi');}public
function
processBlock($parser,$matches){list(,$mType,$mSecond,$mMod1,$mMod2,$mMod3,$mMod4,$mContent)=$matches;$mType=trim(strtolower($mType));$mSecond=trim(strtolower($mSecond));$mContent=trim($mContent,"\n");if(!$mType)$mType='pre';if($mType=='notexy')$mType='html';if($mType=='html'&&!$this->allowed->html)$mType='text';if($mType=='code'||$mType=='samp')$mType=$this->allowed->pre?$mType:'none';elseif(!$this->allowed->$mType)$mType='none';switch($mType){case'none':case'div':$el=new
TexyBlockElement($this->texy);$el->tag='div';$el->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);if($spaces=strspn($mContent,' '))$mContent=preg_replace("#^ {1,$spaces}#m",'',$mContent);if($this->divHandler)if(call_user_func_array($this->divHandler,array($el,&$mContent))===FALSE)return;$el->parse($mContent);$parser->element->appendChild($el);break;case'source':$el=new
TexySourceBlockElement($this->texy);$el->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);if($spaces=strspn($mContent,' '))$mContent=preg_replace("#^ {1,$spaces}#m",'',$mContent);$el->parse($mContent);$parser->element->appendChild($el);break;case'comment':break;case'html':$el=new
TexyHtmlBlockElement($this->texy);$el->parse($mContent);if($this->htmlHandler)if(call_user_func_array($this->htmlHandler,array($el,TRUE))===FALSE)return;$parser->element->appendChild($el);break;case'text':$el=new
TexyTextualElement($this->texy);$el->setContent((nl2br(TexyHtml::htmlChars($mContent))),TRUE);if($this->htmlHandler)if(call_user_func_array($this->htmlHandler,array($el,FALSE))===FALSE)return;$parser->element->appendChild($el);break;default:$el=new
TexyCodeBlockElement($this->texy);$el->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);$el->type=$mType;$el->lang=$mSecond;if($spaces=strspn($mContent,' '))$mContent=preg_replace("#^ {1,$spaces}#m",'',$mContent);$el->setContent($mContent,FALSE);if($this->codeHandler)if(call_user_func_array($this->codeHandler,array($el))===FALSE)return;$parser->element->appendChild($el);}}}class
TexyCodeBlockElement
extends
TexyTextualElement{public$tag='pre';public$lang;public$type;protected
function
generateTags(&$tags){parent::generateTags($tags);if($this->tag){$tags[$this->tag]['class'][]=$this->lang;if($this->type)$tags[$this->type]=array();}}}class
TexyHtmlBlockElement
extends
TexyTextualElement{public
function
parse($text){$parser=new
TexyHtmlParser($this);$parser->parse($text);}}class
TexySourceBlockElement
extends
TexyBlockElement{public$tag='pre';protected
function
generateContent(){$html=parent::generateContent();if($this->texy->formatterModule)$html=$this->texy->formatterModule->postProcess($html);$el=new
TexyCodeBlockElement($this->texy);$el->lang='html';$el->type='code';$el->setContent($html,FALSE);if($this->texy->blockModule->codeHandler)call_user_func_array($this->texy->blockModule->codeHandler,array($el));return$el->safeContent();}} 

class
TexyFormatterModule
extends
TexyModule{public$baseIndent=0;public$lineWrap=80;public$indent=TRUE;private$space;private$hashTable=array();public
function
postProcess($text){if(!$this->allowed)return$text;$this->space=$this->baseIndent;$text=preg_replace_callback('#<(pre|textarea|script|style)(.*)</\\1>#Uis',array($this,'_freeze'),$text);$text=str_replace("\n",' ',$text);$text=preg_replace('# +#',' ',$text);$text=preg_replace_callback('# *<(/?)('.implode(array_keys(TexyHtml::$block),'|').'|br)(>| [^>]*>) *#i',array($this,'indent'),$text);$text=preg_replace("#[\t ]+(\n|\r|$)#",'$1',$text);$text=strtr($text,array("\r\r"=>"\n","\r"=>"\n"));$text=strtr($text,array("\t\x08"=>'',"\x08"=>''));if($this->lineWrap>0)$text=preg_replace_callback('#^(\t*)(.*)$#m',array($this,'wrap'),$text);$text=strtr($text,$this->hashTable);return$text;}private
function
_freeze($matches){static$counter=0;$key='<'.$matches[1].'>'."\x1A".strtr(base_convert(++$counter,10,4),'0123',"\x1B\x1C\x1D\x1E")."\x1A".'</'.$matches[1].'>';$this->hashTable[$key]=$matches[0];return$key;}private
function
indent($matches){list($match,$mClosing,$mTag)=$matches;$match=trim($match);$mTag=strtolower($mTag);if($mTag==='br')return"\n".str_repeat("\t",max(0,$this->space-1)).$match;if(isset(TexyHtml::$empty[$mTag]))return"\r".str_repeat("\t",$this->space).$match."\r".str_repeat("\t",$this->space);if($mClosing==='/'){return"\x08".$match."\n".str_repeat("\t",--$this->space);}return"\n".str_repeat("\t",$this->space++).$match;}private
function
wrap($matches){list(,$mSpace,$mContent)=$matches;return$mSpace.str_replace("\n","\n".$mSpace,wordwrap($mContent,$this->lineWrap));}} 

class
TexyGenericBlockModule
extends
TexyModule{public$handler;public$mergeMode=TRUE;public
function
processBlock($parser,$content){$str_blocks=$this->mergeMode?preg_split('#(\n{2,})#',$content):preg_split('#(\n(?! )|\n{2,})#',$content);foreach($str_blocks
as$str){$str=trim($str);if($str=='')continue;$this->processSingleBlock($parser,$str);}}public
function
processSingleBlock($parser,$content){preg_match($this->texy->translatePattern('#^(.*)<MODIFIER_H>?(\n.*)?()$#sU'),$content,$matches);list(,$mContent,$mMod1,$mMod2,$mMod3,$mMod4,$mContent2)=$matches;$mContent=trim($mContent.$mContent2);if($this->texy->mergeLines){$mContent=preg_replace('#\n (\S)#'," \r\\1",$mContent);$mContent=strtr($mContent,"\n\r"," \n");}$el=new
TexyGenericBlockElement($this->texy);$el->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);$el->parse($mContent);if($el->contentType==TexyDomElement::CONTENT_TEXTUAL)$el->tag='p';elseif($mMod1||$mMod2||$mMod3||$mMod4)$el->tag='div';elseif($el->contentType==TexyDomElement::CONTENT_BLOCK)$el->tag='';else$el->tag='div';if($el->tag&&(strpos($el->getContent(),"\n")!==FALSE)){$elBr=new
TexyTextualElement($this->texy);$elBr->tag='br';$el->setContent(strtr($el->getContent(),array("\n"=>$el->appendChild($elBr))),TRUE);}if($this->handler)if(call_user_func_array($this->handler,array($el))===FALSE)return;$parser->element->appendChild($el);}}class
TexyGenericBlockElement
extends
TexyTextualElement{public$tag='p';} 

class
TexyHeadingModule
extends
TexyModule{const
DYNAMIC=1,FIXED=2;public$handler;public$allowed;public$top=1;public$title;public$balancing=TexyHeadingModule::DYNAMIC;public$levels=array('#'=>0,'*'=>1,'='=>2,'-'=>3,);private$_rangeUnderline;private$_deltaUnderline;private$_rangeSurround;private$_deltaSurround;public
function
__construct($texy){parent::__construct($texy);$this->allowed=(object)NULL;$this->allowed->surrounded=TRUE;$this->allowed->underlined=TRUE;}public
function
init(){if($this->allowed->underlined)$this->texy->registerBlockPattern($this,'processBlockUnderline','#^(\S.*)<MODIFIER_H>?\n'.'(\#|\*|\=|\-){3,}$#mU');if($this->allowed->surrounded)$this->texy->registerBlockPattern($this,'processBlockSurround','#^((\#|\=){2,})(?!\\2)(.+)\\2*<MODIFIER_H>?()$#mU');}public
function
preProcess($text){$this->_rangeUnderline=array(10,0);$this->_rangeSurround=array(10,0);$this->title=NULL;$foo=NULL;$this->_deltaUnderline=&$foo;$bar=NULL;$this->_deltaSurround=&$bar;return$text;}public
function
processBlockUnderline($parser,$matches){list(,$mContent,$mMod1,$mMod2,$mMod3,$mMod4,$mLine)=$matches;$el=new
TexyHeadingElement($this->texy);$el->level=$this->levels[$mLine];if($this->balancing==self::DYNAMIC)$el->deltaLevel=&$this->_deltaUnderline;$el->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);$el->parse(trim($mContent));if($this->handler)if(call_user_func_array($this->handler,array($el))===FALSE)return;$parser->element->appendChild($el);if($this->title===NULL)$this->title=strip_tags($el->toHtml());$this->_rangeUnderline[0]=min($this->_rangeUnderline[0],$el->level);$this->_rangeUnderline[1]=max($this->_rangeUnderline[1],$el->level);$this->_deltaUnderline=-$this->_rangeUnderline[0];$this->_deltaSurround=-$this->_rangeSurround[0]+($this->_rangeUnderline[1]?($this->_rangeUnderline[1]-$this->_rangeUnderline[0]+1):0);}public
function
processBlockSurround($parser,$matches){list(,$mLine,$mChar,$mContent,$mMod1,$mMod2,$mMod3,$mMod4)=$matches;$el=new
TexyHeadingElement($this->texy);$el->level=7-min(7,max(2,strlen($mLine)));if($this->balancing==self::DYNAMIC)$el->deltaLevel=&$this->_deltaSurround;$el->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);$el->parse(trim($mContent));if($this->handler)if(call_user_func_array($this->handler,array($el))===FALSE)return;$parser->element->appendChild($el);if($this->title===NULL)$this->title=strip_tags($el->toHtml());$this->_rangeSurround[0]=min($this->_rangeSurround[0],$el->level);$this->_rangeSurround[1]=max($this->_rangeSurround[1],$el->level);$this->_deltaSurround=-$this->_rangeSurround[0]+($this->_rangeUnderline[1]?($this->_rangeUnderline[1]-$this->_rangeUnderline[0]+1):0);}}class
TexyHeadingElement
extends
TexyTextualElement{public$level=0;public$deltaLevel=0;protected
function
generateTags(&$tags){$this->tag='h'.min(6,max(1,$this->level+$this->deltaLevel+$this->texy->headingModule->top));parent::generateTags($tags);}} 

class
TexyHorizLineModule
extends
TexyModule{public$handler;public
function
init(){$this->texy->registerBlockPattern($this,'processBlock','#^(\- |\-|\* |\*){3,}\ *<MODIFIER_H>?()$#mU');}public
function
processBlock($parser,$matches){list(,$mLine,$mMod1,$mMod2,$mMod3,$mMod4)=$matches;$el=new
TexyBlockElement($this->texy);$el->tag='hr';$el->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);if($this->handler)if(call_user_func_array($this->handler,array($el))===FALSE)return;$parser->element->appendChild($el);}} 

class
TexyHtmlModule
extends
TexyModule{public$handler;public$allowed;public$allowedComments=TRUE;public$safeTags=array('a'=>array('href','rel','title','lang'),'abbr'=>array('title','lang'),'acronym'=>array('title','lang'),'b'=>array('title','lang'),'br'=>array(),'cite'=>array('title','lang'),'code'=>array('title','lang'),'dfn'=>array('title','lang'),'em'=>array('title','lang'),'i'=>array('title','lang'),'kbd'=>array('title','lang'),'q'=>array('cite','title','lang'),'samp'=>array('title','lang'),'small'=>array('title','lang'),'span'=>array('title','lang'),'strong'=>array('title','lang'),'sub'=>array('title','lang'),'sup'=>array('title','lang'),'var'=>array('title','lang'),);public
function
__construct($texy){parent::__construct($texy);$this->allowed=&$texy->allowedTags;}public
function
init(){$this->texy->registerLinePattern($this,'process','#<(/?)([a-z][a-z0-9_:-]*)(|\s(?:[\sa-z0-9:-]|=\s*"[^":HASH:]*"|=\s*\'[^\':HASH:]*\'|=[^>:HASH:]*)*)(/?)>|<!--([^:HASH:]*?)-->#is');}public
function
process($parser,$matches){list($match,$mClosing,$mTag,$mAttr,$mEmpty)=$matches;if($mTag==''){if(!$this->allowedComments)return
substr($matches[5],0,1)=='['?$match:'';$el=new
TexyTextualElement($this->texy);$el->contentType=TexyDomElement::CONTENT_NONE;$el->setContent($match,TRUE);return$parser->element->appendChild($el);}$allowedTags=&$this->texy->allowedTags;if(!$allowedTags)return$match;$tag=strtolower($mTag);if(!isset(TexyHtml::$valid[$tag]))$tag=$mTag;$empty=($mEmpty=='/')||isset(TexyHtml::$empty[$tag]);$isOpening=$mClosing!='/';if($empty&&!$isOpening)return$match;if(is_array($allowedTags)&&!isset($allowedTags[$tag]))return$match;$el=new
TexyHtmlTagElement($this->texy);$el->contentType=isset(TexyHtml::$inline[$tag])?TexyDomElement::CONTENT_NONE:TexyDomElement::CONTENT_BLOCK;if($isOpening){$attrs=array();$allowedAttrs=is_array($allowedTags)?$allowedTags[$tag]:NULL;preg_match_all('#([a-z0-9:-]+)\s*(?:=\s*(\'[^\']*\'|"[^"]*"|[^\'"\s]+))?()#is',$mAttr,$matchesAttr,PREG_SET_ORDER);foreach($matchesAttr
as$matchAttr){$key=strtolower($matchAttr[1]);if(is_array($allowedAttrs)&&!in_array($key,$allowedAttrs))continue;$value=$matchAttr[2];if($value==NULL)$value=$key;elseif($value{0}=='\''||$value{0}=='"')$value=substr($value,1,-1);$attrs[$key]=$value;}$modifier=new
TexyModifier($this->texy);if(isset($attrs['class'])){$modifier->parseClasses($attrs['class']);$attrs['class']=$modifier->classes;}if(isset($attrs['style'])){$modifier->parseStyles($attrs['style']);$attrs['style']=$modifier->styles;}if(isset($attrs['id'])){if(!$this->texy->allowedClasses)unset($attrs['id']);elseif(is_array($this->texy->allowedClasses)&&!in_array('#'.$attrs['id'],$this->texy->allowedClasses))unset($attrs['id']);}switch($tag){case'img':if(!isset($attrs['src']))return$match;$this->texy->summary['images'][]=$attrs['src'];break;case'a':if(!isset($attrs['href'])&&!isset($attrs['name'])&&!isset($attrs['id']))return$match;if(isset($attrs['href'])){$this->texy->summary['links'][]=$attrs['href'];}}if($empty)$attrs[TexyHtml::EMPTYTAG]=TRUE;$el->tags[$tag]=$attrs;$el->isOpening=TRUE;}else{$el->tags[$tag]=FALSE;$el->isOpening=FALSE;}if($this->handler)if(call_user_func_array($this->handler,array($el))===FALSE)return'';return$parser->element->appendChild($el);}public
function
trustMode($onlyValidTags=TRUE){$this->texy->allowedTags=$onlyValidTags?TexyHtml::$valid:Texy::ALL;}public
function
safeMode($allowSafeTags=TRUE){$this->texy->allowedTags=$allowSafeTags?$this->safeTags:Texy::NONE;}}class
TexyHtmlTagElement
extends
TexyDomElement{public$tags;public$isOpening;public
function
toHtml(){if($this->isOpening)return
TexyHtml::openingTags($this->tags);else
return
TexyHtml::closingTags($this->tags);}} 

class
TexyImageModule
extends
TexyModule{public$handler;public$root='images/';public$linkedRoot='images/';public$rootPrefix='';public$leftClass=NULL;public$rightClass=NULL;public$defaultAlt='';public
function
__construct($texy){parent::__construct($texy);if(isset($_SERVER['SCRIPT_NAME'])){$this->rootPrefix=dirname($_SERVER['SCRIPT_NAME']).'/';}}public
function
init(){$this->texy->registerLinePattern($this,'processLine','#'.TEXY_PATTERN_IMAGE.TEXY_PATTERN_LINK_N.'??()#U');}public
function
addReference($name,$obj){$this->texy->addReference('*'.$name.'*',$obj);}public
function
getReference($name){$el=$this->texy->getReference('*'.$name.'*');if($el
instanceof
TexyImageReference)return$el;else
return
FALSE;}public
function
preProcess($text){return
preg_replace_callback('#^\[\*([^\n]+)\*\]:\ +(.+)\ *'.TEXY_PATTERN_MODIFIER.'?()$#mU',array($this,'processReferenceDefinition'),$text);}public
function
processReferenceDefinition($matches){list(,$mRef,$mURLs,$mMod1,$mMod2,$mMod3)=$matches;$elRef=new
TexyImageReference($this->texy,$mURLs);$elRef->modifier->setProperties($mMod1,$mMod2,$mMod3);$this->addReference($mRef,$elRef);return'';}public
function
processLine($parser,$matches){if(!$this->allowed)return'';list(,$mURLs,$mMod1,$mMod2,$mMod3,$mMod4,$mLink)=$matches;$elImage=new
TexyImageElement($this->texy);$elImage->setImagesRaw($mURLs);$elImage->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);if($mLink){$elLink=new
TexyLinkElement($this->texy);if($mLink==':'){$elImage->requireLinkImage();$elLink->link->copyFrom($elImage->linkImage);}else{$elLink->setLinkRaw($mLink);}return$parser->element->appendChild($elLink,$parser->element->appendChild($elImage));}if($this->handler)if(call_user_func_array($this->handler,array($elImage))===FALSE)return'';return$parser->element->appendChild($elImage);}}class
TexyImageReference{public$URLs;public$modifier;public
function
__construct($texy,$URLs=NULL){$this->modifier=new
TexyModifier($texy);$this->URLs=$URLs;}}class
TexyImageElement
extends
TexyHtmlElement{public$image;public$overImage;public$linkImage;public$width,$height;public
function
__construct($texy){parent::__construct($texy);$this->image=new
TexyUrl($texy);$this->overImage=new
TexyUrl($texy);$this->linkImage=new
TexyUrl($texy);}public
function
setImages($URL=NULL,$URL_over=NULL,$URL_link=NULL){$this->image->set($URL,$this->texy->imageModule->root,TRUE);$this->overImage->set($URL_over,$this->texy->imageModule->root,TRUE);$this->linkImage->set($URL_link,$this->texy->imageModule->linkedRoot,TRUE);}public
function
setSize($width,$height){$this->width=abs((int)$width);$this->height=abs((int)$height);}public
function
setImagesRaw($URLs){$elRef=$this->texy->imageModule->getReference(trim($URLs));if($elRef){$URLs=$elRef->URLs;$this->modifier->copyFrom($elRef->modifier);}$URLs=explode('|',$URLs.'||');if(preg_match('#^(.*) (?:(\d+)|\?) *x *(?:(\d+)|\?) *()$#U',$URLs[0],$matches)){$URLs[0]=$matches[1];$this->setSize($matches[2],$matches[3]);}$this->setImages($URLs[0],$URLs[1],$URLs[2]);}protected
function
generateTags(&$tags){if($this->image->asURL()=='')return;$attrs=$this->modifier->getAttrs('img');$attrs['class']=$this->modifier->classes;$attrs['style']=$this->modifier->styles;$attrs['id']=$this->modifier->id;if($this->modifier->hAlign==TexyModifier::HALIGN_LEFT){if($this->texy->imageModule->leftClass!='')$attrs['class'][]=$this->texy->imageModule->leftClass;else$attrs['style']['float']='left';}elseif($this->modifier->hAlign==TexyModifier::HALIGN_RIGHT){if($this->texy->imageModule->rightClass!='')$attrs['class'][]=$this->texy->imageModule->rightClass;else$attrs['style']['float']='right';}if($this->modifier->vAlign)$attrs['style']['vertical-align']=$this->modifier->vAlign;$this->requireSize();if($this->width)$attrs['width']=$this->width;if($this->height)$attrs['height']=$this->height;$this->texy->summary['images'][]=$attrs['src']=$this->image->asURL();if($this->overImage->asURL()){$attrs['onmouseover']='this.src=\''.$this->overImage->asURL().'\'';$attrs['onmouseout']='this.src=\''.$this->image->asURL().'\'';$this->texy->summary['preload'][]=$this->overImage->asURL();}$attrs['alt']=$this->modifier->title!=NULL?$this->modifier->title:$this->texy->imageModule->defaultAlt;$tags['img']=$attrs;}protected
function
requireSize(){if($this->width)return;$file=$this->texy->imageModule->rootPrefix.'/'.$this->image->asURL();if(!is_file($file))return
FALSE;$size=getImageSize($file);if(!is_array($size))return
FALSE;$this->setSize($size[0],$size[1]);}public
function
requireLinkImage(){if($this->linkImage->asURL()=='')$this->linkImage->set($this->image->value,$this->texy->imageModule->linkedRoot,TRUE);}} 

class
TexyImageDescModule
extends
TexyModule{public$handler;public$boxClass='image';public$leftClass='image left';public$rightClass='image right';public
function
init(){if($this->texy->imageModule->allowed)$this->texy->registerBlockPattern($this,'processBlock','#^'.TEXY_PATTERN_IMAGE.TEXY_PATTERN_LINK_N.'?? +\*\*\* +(.*)<MODIFIER_H>?()$#mU');}public
function
processBlock($parser,$matches){list(,$mURLs,$mImgMod1,$mImgMod2,$mImgMod3,$mImgMod4,$mLink,$mContent,$mMod1,$mMod2,$mMod3,$mMod4)=$matches;$el=new
TexyImageDescElement($this->texy);$el->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);$elImage=new
TexyImageElement($this->texy);$elImage->setImagesRaw($mURLs);$elImage->modifier->setProperties($mImgMod1,$mImgMod2,$mImgMod3,$mImgMod4);$el->modifier->hAlign=$elImage->modifier->hAlign;$elImage->modifier->hAlign=NULL;$content=$el->appendChild($elImage);if($mLink){$elLink=new
TexyLinkElement($this->texy);if($mLink==':'){$elImage->requireLinkImage();$elLink->link->copyFrom($elImage->linkImage);}else{$elLink->setLinkRaw($mLink);}$content=$el->appendChild($elLink,$content);}$elDesc=new
TexyGenericBlockElement($this->texy);$elDesc->parse(ltrim($mContent));$content.=$el->appendChild($elDesc);$el->setContent($content,TRUE);if($this->handler)if(call_user_func_array($this->handler,array($el))===FALSE)return;$parser->element->appendChild($el);}}class
TexyImageDescElement
extends
TexyTextualElement{protected
function
generateTags(&$tags){$attrs=$this->modifier->getAttrs('div');$attrs['class']=$this->modifier->classes;$attrs['style']=$this->modifier->styles;$attrs['id']=$this->modifier->id;if($this->modifier->hAlign==TexyModifier::HALIGN_LEFT){$attrs['class'][]=$this->texy->imageDescModule->leftClass;}elseif($this->modifier->hAlign==TexyModifier::HALIGN_RIGHT){$attrs['class'][]=$this->texy->imageDescModule->rightClass;}elseif($this->texy->imageDescModule->boxClass)$attrs['class'][]=$this->texy->imageDescModule->boxClass;$tags['div']=$attrs;}} 

class
TexyLinkModule
extends
TexyModule{public$handler;public$allowed;public$root='';public$emailOnClick=NULL;public$imageOnClick='return !popupImage(this.href)';public$popupOnClick='return !popup(this.href)';public$forceNoFollow=FALSE;public
function
__construct($texy){parent::__construct($texy);$this->allowed=(object)NULL;$this->allowed->link=TRUE;$this->allowed->email=TRUE;$this->allowed->url=TRUE;$this->allowed->quickLink=TRUE;$this->allowed->references=TRUE;}public
function
init(){if($this->allowed->quickLink)$this->texy->registerLinePattern($this,'processLineQuick','#([:CHAR:0-9@\#$%&.,_-]+)(?=:\[)<LINK>()#U<UTF>');$this->texy->registerLinePattern($this,'processLineReference','#('.TEXY_PATTERN_LINK_REF.')#U');if($this->allowed->url)$this->texy->registerLinePattern($this,'processLineURL','#(?<=\s|^|\(|\[|\<|:)(?:https?://|www\.|ftp://|ftp\.)[a-z0-9.-][/a-z\d+\.~%&?@=_:;\#,-]+[/\w\d+~%?@=_\#]#i<UTF>');if($this->allowed->email)$this->texy->registerLinePattern($this,'processLineURL','#(?<=\s|^|\(|\[|\<|:)'.TEXY_PATTERN_EMAIL.'#i');}public
function
addReference($name,$obj){$this->texy->addReference($name,$obj);}public
function
getReference($refName){$el=$this->texy->getReference($refName);$query='';if(!$el){$queryPos=strpos($refName,'?');if($queryPos===FALSE)$queryPos=strpos($refName,'#');if($queryPos!==FALSE){$el=$this->texy->getReference(substr($refName,0,$queryPos));$query=substr($refName,$queryPos);}}if(!($el
instanceof
TexyLinkReference))return
FALSE;$el->query=$query;return$el;}public
function
preProcess($text){if($this->allowed->references)return
preg_replace_callback('#^\[([^\[\]\#\?\*\n]+)\]: +('.TEXY_PATTERN_LINK_IMAGE.'|(?!\[)\S+)(\ .+)?'.TEXY_PATTERN_MODIFIER.'?()$#mU',array($this,'processReferenceDefinition'),$text);return$text;}public
function
processReferenceDefinition($matches){list(,$mRef,$mLink,$mLabel,$mMod1,$mMod2,$mMod3)=$matches;$elRef=new
TexyLinkReference($this->texy,$mLink,$mLabel);$elRef->modifier->setProperties($mMod1,$mMod2,$mMod3);$this->addReference($mRef,$elRef);return'';}public
function
processLineQuick($parser,$matches){list(,$mContent,$mLink)=$matches;if(!$this->allowed->quickLink)return$mContent;$elLink=new
TexyLinkElement($this->texy);$elLink->setLinkRaw($mLink,$mContent);return$parser->element->appendChild($elLink,$mContent);}public
function
processLineReference($parser,$matches){list($match,$mRef)=$matches;if(!$this->allowed->link)return$match;$elLink=new
TexyLinkRefElement($this->texy);if($elLink->setLink($mRef)===FALSE)return$match;return$parser->element->appendChild($elLink);}public
function
processLineURL($parser,$matches){list($mURL)=$matches;$elLink=new
TexyLinkElement($this->texy);$elLink->setLinkRaw($mURL);return$parser->element->appendChild($elLink,$elLink->link->asTextual());}}class
TexyLinkReference{public$URL;public$query;public$label;public$modifier;public
function
__construct($texy,$URL=NULL,$label=NULL){$this->modifier=new
TexyModifier($texy);if(strlen($URL)>1)if($URL{0}=='\''||$URL{0}=='"')$URL=substr($URL,1,-1);$this->URL=trim($URL);$this->label=trim($label);}}class
TexyLinkElement
extends
TexyInlineTagElement{public$link;public
function
__construct($texy){parent::__construct($texy);$this->link=new
TexyUrl($texy);}public
function
setLink($URL){$this->link->set($URL,$this->texy->linkModule->root);}public
function
setLinkRaw($link,$text=''){if(@$link{0}=='['&&@$link{1}!='*'){$elRef=$this->texy->linkModule->getReference(substr($link,1,-1));if($elRef){$this->modifier->copyFrom($elRef->modifier);$link=$elRef->URL.$elRef->query;$link=str_replace('%s',urlencode(Texy::wash($text)),$link);}else{$this->setLink(substr($link,1,-1));return;}}if(strlen($link)>1&&$link{0}=='['&&$link{1}=='*'){$elImage=new
TexyImageElement($this->texy);$elImage->setImagesRaw(substr($link,2,-2));$elImage->requireLinkImage();$this->link->copyFrom($elImage->linkImage);return;}$this->setLink($link);}protected
function
generateTags(&$tags){if($this->link->asURL()=='')return;$attrs=$this->modifier->getAttrs('a');$this->texy->summary['links'][]=$attrs['href']=$this->link->asURL();$nofollowClass=in_array('nofollow',$this->modifier->unfilteredClasses);if($this->link->isAbsolute()&&($this->texy->linkModule->forceNoFollow||$nofollowClass))$attrs['rel']='nofollow';$attrs['id']=$this->modifier->id;$attrs['title']=$this->modifier->title;$attrs['class']=$this->modifier->classes;$attrs['style']=$this->modifier->styles;if($nofollowClass){if(($pos=array_search('nofollow',$attrs['class']))!==FALSE)unset($attrs['class'][$pos]);}$popup=in_array('popup',$this->modifier->unfilteredClasses);if($popup){if(($pos=array_search('popup',$attrs['class']))!==FALSE)unset($attrs['class'][$pos]);$attrs['onclick']=$this->texy->linkModule->popupOnClick;}if($this->link->isEmail())$attrs['onclick']=$this->texy->linkModule->emailOnClick;if($this->link->isImage())$attrs['onclick']=$this->texy->linkModule->imageOnClick;$tags['a']=$attrs;}}class
TexyLinkRefElement
extends
TexyTextualElement{public$refName;public$contentType=TexyDomElement::CONTENT_TEXTUAL;static
private$callstack;public
function
setLink($refRaw){$this->refName=substr($refRaw,1,-1);$lowName=strtolower($this->refName);if(isset(self::$callstack[$lowName]))return
FALSE;$elRef=$this->texy->linkModule->getReference($this->refName);if(!$elRef)return
FALSE;$elLink=new
TexyLinkElement($this->texy);$elLink->setLinkRaw($refRaw);if($elRef->label){self::$callstack[$lowName]=TRUE;$this->parse($elRef->label);unset(self::$callstack[$lowName]);}else{$this->setContent($elLink->link->asTextual(),TRUE);}$this->setContent($this->appendChild($elLink,$this->content),TRUE);}} 

class
TexyListModule
extends
TexyModule{public$handler;public$allowed=array('*'=>TRUE,'-'=>TRUE,'+'=>TRUE,'1.'=>TRUE,'1)'=>TRUE,'I.'=>TRUE,'I)'=>TRUE,'a)'=>TRUE,'A)'=>TRUE,);public$translate=array('*'=>array('\*','','','ul'),'-'=>array('\-','','','ul'),'+'=>array('\+','','','ul'),'1.'=>array('\d+\.\ ','','','ol'),'1)'=>array('\d+\)','','','ol'),'I.'=>array('[IVX]+\.\ ','','upper-roman','ol'),'I)'=>array('[IVX]+\)','','upper-roman','ol'),'a)'=>array('[a-z]\)','','lower-alpha','ol'),'A)'=>array('[A-Z]\)','','upper-alpha','ol'),);public
function
init(){$bullets=array();foreach($this->allowed
as$bullet=>$allowed)if($allowed)$bullets[]=$this->translate[$bullet][0];$this->texy->registerBlockPattern($this,'processBlock','#^(?:<MODIFIER_H>\n)?'.'('.implode('|',$bullets).')(\n?)\ +\S.*$#mU');}public
function
processBlock($parser,$matches){list(,$mMod1,$mMod2,$mMod3,$mMod4,$mBullet,$mNewLine)=$matches;$texy=$this->texy;$el=new
TexyListElement($texy);$el->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);$bullet='';foreach($this->translate
as$type)if(preg_match('#'.$type[0].'#A',$mBullet)){$bullet=$type[0];$el->tag=$type[3];$el->modifier->styles['list-style-type']=$type[2];$el->modifier->classes[]=$type[1];break;}$parser->moveBackward($mNewLine?2:1);$count=0;while($elItem=$this->processItem($parser,$bullet)){$el->appendChild($elItem);$count++;}if(!$count)return
FALSE;if($this->handler)if(call_user_func_array($this->handler,array($el))===FALSE)return;$parser->element->appendChild($el);}public
function
processItem($parser,$bullet,$indented=FALSE){$texy=$this->texy;$spacesBase=$indented?('\ {1,}'):'';$patternItem=$texy->translatePattern("#^\n?($spacesBase)$bullet(\n?)(\ +)(\S.*)?<MODIFIER_H>?()$#mAU");if(!$parser->receiveNext($patternItem,$matches)){return
FALSE;}list(,$mIndent,$mNewLine,$mSpace,$mContent,$mMod1,$mMod2,$mMod3,$mMod4)=$matches;$elItem=new
TexyListItemElement($texy);$elItem->tag='li';$elItem->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);$spaces=$mNewLine?strlen($mSpace):'';$content=' '.$mContent;while($parser->receiveNext('#^(\n*)'.$mIndent.'(\ {1,'.$spaces.'})(.*)()$#Am',$matches)){list(,$mBlank,$mSpaces,$mContent)=$matches;if($spaces==='')$spaces=strlen($mSpaces);$content.=TEXY_NEWLINE.$mBlank.$mContent;}$mergeMode=&$texy->genericBlockModule->mergeMode;$tmp=$mergeMode;$mergeMode=FALSE;$elItem->parse($content);$mergeMode=$tmp;if($elItem->getChild(0)instanceof
TexyGenericBlockElement)$elItem->getChild(0)->tag='';return$elItem;}}class
TexyListElement
extends
TexyBlockElement{}class
TexyListItemElement
extends
TexyBlockElement{} 

class
TexyDefinitionListModule
extends
TexyListModule{public$handler;public$allowed=array('*'=>TRUE,'-'=>TRUE,'+'=>TRUE,);public$translate=array('*'=>array('\*',''),'-'=>array('\-',''),'+'=>array('\+',''),);public
function
init(){$bullets=array();foreach($this->allowed
as$bullet=>$allowed)if($allowed)$bullets[]=$this->translate[$bullet][0];$this->texy->registerBlockPattern($this,'processBlock','#^(?:<MODIFIER_H>\n)?'.'(\S.*)\:\ *<MODIFIER_H>?\n'.'(\ +)('.implode('|',$bullets).')\ +\S.*$#mU');}public
function
processBlock($parser,$matches){list(,$mMod1,$mMod2,$mMod3,$mMod4,$mContentTerm,$mModTerm1,$mModTerm2,$mModTerm3,$mModTerm4,$mSpaces,$mBullet)=$matches;$texy=$this->texy;$el=new
TexyListElement($texy);$el->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);$el->tag='dl';$bullet='';foreach($this->translate
as$type)if(preg_match('#'.$type[0].'#A',$mBullet)){$bullet=$type[0];$el->modifier->classes[]=$type[1];break;}$parser->moveBackward(2);$patternTerm=$texy->translatePattern('#^\n?(\S.*)\:\ *<MODIFIER_H>?()$#mUA');$bullet=preg_quote($mBullet);while(TRUE){if($elItem=$this->processItem($parser,preg_quote($mBullet),TRUE)){$elItem->tag='dd';$el->appendChild($elItem);continue;}if($parser->receiveNext($patternTerm,$matches)){list(,$mContent,$mMod1,$mMod2,$mMod3,$mMod4)=$matches;$elItem=new
TexyTextualElement($texy);$elItem->tag='dt';$elItem->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);$elItem->parse($mContent);$el->appendChild($elItem);continue;}break;}if($this->handler)if(call_user_func_array($this->handler,array($el))===FALSE)return;$parser->element->appendChild($el);}} 

class
TexyLongWordsModule
extends
TexyModule{public$wordLimit=20;public$shy='&#173;';public$nbsp='&#160;';public
function
linePostProcess($text){if(!$this->allowed)return$text;$charShy=$this->texy->utf?"\xC2\xAD":"\xAD";$charNbsp=$this->texy->utf?"\xC2\xA0":"\xA0";$text=strtr($text,array('&shy;'=>$charShy,'&#173;'=>$charShy,'&nbsp;'=>$charNbsp,'&#160;'=>$charNbsp,));$text=preg_replace_callback($this->texy->translatePattern('#[^\ \n\t\-\xAD'.TEXY_HASH_SPACES.']{'.$this->wordLimit.',}#<UTF>'),array($this,'_replace'),$text);$text=strtr($text,array($charShy=>$this->shy,$charNbsp=>$this->nbsp,));return$text;}private
function
_replace($matches){list($mWord)=$matches;$chars=array();preg_match_all($this->texy->translatePattern('#&\\#?[a-z0-9]+;|[:HASH:]+|.#<UTF>'),$mWord,$chars);$chars=$chars[0];if(count($chars)<$this->wordLimit)return$mWord;$consonants=array_flip(array('b','c','d','f','g','h','j','k','l','m','n','p','q','r','s','t','v','w','x','z','B','C','D','F','G','H','J','K','L','M','N','P','Q','R','S','T','V','W','X','Z',"\xe8","\xef","\xf2","\xf8","\x9a","\x9d","\x9e","\xc8","\xcf","\xd2","\xd8","\x8a","\x8d","\x8e","\xc4\x8d","\xc4\x8f","\xc5\x88","\xc5\x99","\xc5\xa1","\xc5\xa5","\xc5\xbe","\xc4\x8c","\xc4\x8e","\xc5\x87","\xc5\x98","\xc5\xa0","\xc5\xa4","\xc5\xbd"));$vowels=array_flip(array('a','e','i','o','u','y','A','E','I','O','U','Y',"\xe1","\xe9","\xec","\xed","\xf3","\xfa","\xf9","\xfd","\xc1","\xc9","\xcc","\xcd","\xd3","\xda","\xd9","\xdd","\xc3\xa1","\xc3\xa9","\xc4\x9b","\xc3\xad","\xc3\xb3","\xc3\xba","\xc5\xaf","\xc3\xbd","\xc3\x81","\xc3\x89","\xc4\x9a","\xc3\x8d","\xc3\x93","\xc3\x9a","\xc5\xae","\xc3\x9d"));$before_r=array_flip(array('b','B','c','C','d','D','f','F','g','G','k','K','p','P','r','R','t','T','v','V',"\xe8","\xc8","\xef","\xcf","\xf8","\xd8","\x9d","\x8d","\xc4\x8d","\xc4\x8c","\xc4\x8f","\xc4\x8e","\xc5\x99","\xc5\x98","\xc5\xa5","\xc5\xa4"));$before_l=array_flip(array('b','B','c','C','d','D','f','F','g','G','k','K','l','L','p','P','t','T','v','V',"\xe8","\xc8","\xef","\xcf","\x9d","\x8d","\xc4\x8d","\xc4\x8c","\xc4\x8f","\xc4\x8e","\xc5\xa5","\xc5\xa4"));$before_h=array_flip(array('c','C','s','S'));$doubleVowels=array_flip(array('a','A','o','O'));$DONT=0;$HERE=1;$AFTER=2;$s=array();$trans=array();$s[]='';$trans[]=-1;$hashCounter=$len=$counter=0;foreach($chars
as$key=>$char){if(ord($char{0})<32)continue;$s[]=$char;$trans[]=$key;}$s[]='';$len=count($s)-2;$positions=array();$a=1;$last=1;while($a<$len){$hyphen=$DONT;do{if($s[$a]=='.'){$hyphen=$HERE;break;}if(isset($consonants[$s[$a]])){if(isset($vowels[$s[$a+1]])){if(isset($vowels[$s[$a-1]]))$hyphen=$HERE;break;}if(($s[$a]=='s')&&($s[$a-1]=='n')&&isset($consonants[$s[$a+1]])){$hyphen=$AFTER;break;}if(isset($consonants[$s[$a+1]])&&isset($vowels[$s[$a-1]])){if($s[$a+1]=='r'){$hyphen=isset($before_r[$s[$a]])?$HERE:$AFTER;break;}if($s[$a+1]=='l'){$hyphen=isset($before_l[$s[$a]])?$HERE:$AFTER;break;}if($s[$a+1]=='h'){$hyphen=isset($before_h[$s[$a]])?$DONT:$AFTER;break;}$hyphen=$AFTER;break;}break;}if(($s[$a]=='u')&&isset($doubleVowels[$s[$a-1]])){$hyphen=$AFTER;break;}if(in_array($s[$a],$vowels)&&isset($vowels[$s[$a-1]])){$hyphen=$HERE;break;}}while(0);if($hyphen==$DONT&&($a-$last>$this->wordLimit*0.6))$positions[]=$last=$a-1;if($hyphen==$HERE)$positions[]=$last=$a-1;if($hyphen==$AFTER){$positions[]=$last=$a;$a++;}$a++;}$a=end($positions);if(($a==$len-1)&&isset($consonants[$s[$len]]))array_pop($positions);$syllables=array();$last=0;foreach($positions
as$pos){if($pos-$last>$this->wordLimit*0.6){$syllables[]=implode('',array_splice($chars,0,$trans[$pos]-$trans[$last]));$last=$pos;}}$syllables[]=implode('',$chars);$charShy=$this->texy->utf?"\xC2\xAD":"\xAD";$charNbsp=$this->texy->utf?"\xC2\xA0":"\xA0";$text=implode($charShy,$syllables);$text=strtr($text,array($charShy.$charNbsp=>' ',$charNbsp.$charShy=>' '));return$text;}} 

class
TexyPhraseModule
extends
TexyModule{public$codeHandler;public$handler;public$allowed=array('***'=>'strong em','**'=>'strong','*'=>'em','++'=>'ins','--'=>'del','^^'=>'sup','__'=>'sub','"'=>'span','~'=>'span','~~'=>'cite','""()'=>'acronym','()'=>'acronym','`'=>'code','``'=>'',);public
function
init(){if(@$this->allowed['***']!==FALSE)$this->texy->registerLinePattern($this,'processPhrase','#(?<!\*)\*\*\*(?!\ |\*)(.+)<MODIFIER>?(?<!\ |\*)\*\*\*(?!\*)()<LINK>??()#U',$this->allowed['***']);if(@$this->allowed['**']!==FALSE)$this->texy->registerLinePattern($this,'processPhrase','#(?<!\*)\*\*(?!\ |\*)(.+)<MODIFIER>?(?<!\ |\*)\*\*(?!\*)<LINK>??()#U',$this->allowed['**']);if(@$this->allowed['*']!==FALSE)$this->texy->registerLinePattern($this,'processPhrase','#(?<!\*)\*(?!\ |\*)(.+)<MODIFIER>?(?<!\ |\*)\*(?!\*)<LINK>??()#U',$this->allowed['*']);if(@$this->allowed['++']!==FALSE)$this->texy->registerLinePattern($this,'processPhrase','#(?<!\+)\+\+(?!\ |\+)(.+)<MODIFIER>?(?<!\ |\+)\+\+(?!\+)()#U',$this->allowed['++']);if(@$this->allowed['--']!==FALSE)$this->texy->registerLinePattern($this,'processPhrase','#(?<!\-)\-\-(?!\ |\-)(.+)<MODIFIER>?(?<!\ |\-)\-\-(?!\-)()#U',$this->allowed['--']);if(@$this->allowed['^^']!==FALSE)$this->texy->registerLinePattern($this,'processPhrase','#(?<!\^)\^\^(?!\ |\^)(.+)<MODIFIER>?(?<!\ |\^)\^\^(?!\^)()#U',$this->allowed['^^']);if(@$this->allowed['__']!==FALSE)$this->texy->registerLinePattern($this,'processPhrase','#(?<!\_)\_\_(?!\ |\_)(.+)<MODIFIER>?(?<!\ |\_)\_\_(?!\_)()#U',$this->allowed['__']);if(@$this->allowed['"']!==FALSE)$this->texy->registerLinePattern($this,'processPhrase','#(?<!\")\"(?!\ )([^\"]+)<MODIFIER>?(?<!\ )\"(?!\")<LINK>??()#U',$this->allowed['"']);if(@$this->allowed['~']!==FALSE)$this->texy->registerLinePattern($this,'processPhrase','#(?<!\~)\~(?!\ )([^\~]+)<MODIFIER>?(?<!\ )\~(?!\~)<LINK>??()#U',$this->allowed['~']);if(@$this->allowed['~~']!==FALSE)$this->texy->registerLinePattern($this,'processPhrase','#(?<!\~)\~\~(?!\ |\~)(.+)<MODIFIER>?(?<!\ |\~)\~\~(?!\~)<LINK>??()#U',$this->allowed['~~']);if(@$this->allowed['""()']!==FALSE)$this->texy->registerLinePattern($this,'processPhrase','#(?<!\")\"(?!\ )([^\"]+)<MODIFIER>?(?<!\ )\"(?!\")\(\((.+)\)\)()#U',$this->allowed['""()']);if(@$this->allowed['()']!==FALSE)$this->texy->registerLinePattern($this,'processPhrase','#(?<![:CHAR:])([:CHAR:]{2,})()()()\(\((.+)\)\)#U<UTF>',$this->allowed['()']);if(@$this->allowed['``']!==FALSE)$this->texy->registerLinePattern($this,'processProtect','#\`\`(\S[^:HASH:]*)(?<!\ )\`\`()#U',FALSE);if(@$this->allowed['`']!==FALSE)$this->texy->registerLinePattern($this,'processCode','#\`(\S[^:HASH:]*)<MODIFIER>?(?<!\ )\`()#U');$this->texy->registerBlockPattern($this,'processBlock','#^`=(none|code|kbd|samp|var|span)$#mUi');}public
function
processPhrase($parser,$matches,$tags){list($match,$mContent,$mMod1,$mMod2,$mMod3,$mLink)=$matches;if($mContent==NULL){preg_match('#^(.)+(.+)'.TEXY_PATTERN_MODIFIER.'?\\1+()$#U',$match,$matches);list($match,$mDelim,$mContent,$mMod1,$mMod2,$mMod3,$mLink)=$matches;}if(($tags=='span')&&$mLink)$tags='';if(($tags=='span')&&!$mMod1&&!$mMod2&&!$mMod3)return$match;$tags=array_reverse(explode(' ',$tags));$el=NULL;foreach($tags
as$tag){$el=new
TexyInlineTagElement($this->texy);$el->tag=$tag;if($tag=='acronym'||$tag=='abbr'){$el->modifier->title=$mLink;$mLink='';}if($this->handler)if(call_user_func_array($this->handler,array($el,$tags))===FALSE)return'';$mContent=$parser->element->appendChild($el,$mContent);}if($mLink){$el=new
TexyLinkElement($this->texy);$el->setLinkRaw($mLink,$mContent);$mContent=$parser->element->appendChild($el,$mContent);}if($el)$el->modifier->setProperties($mMod1,$mMod2,$mMod3);return$mContent;}public
function
processBlock($parser,$matches){list(,$mTag)=$matches;$this->allowed['`']=strtolower($mTag);if($this->allowed['`']=='none')$this->allowed['`']='';}public
function
processCode($parser,$matches){list(,$mContent,$mMod1,$mMod2,$mMod3)=$matches;$texy=$this->texy;$el=new
TexyTextualElement($texy);$el->modifier->setProperties($mMod1,$mMod2,$mMod3);$el->contentType=TexyDomElement::CONTENT_TEXTUAL;$el->setContent($mContent,FALSE);$el->tag=$this->allowed['`'];if($this->codeHandler)if(call_user_func_array($this->codeHandler,array($el,'code'))===FALSE)return'';$el->safeContent();return$parser->element->appendChild($el);}public
function
processProtect($parser,$matches,$isHtmlSafe=FALSE){list(,$mContent)=$matches;$el=new
TexyTextualElement($this->texy);$el->contentType=TexyDomElement::CONTENT_TEXTUAL;$el->setContent(Texy::freezeSpaces($mContent),$isHtmlSafe);return$parser->element->appendChild($el);}} 

class
TexyQuickCorrectModule
extends
TexyModule{public$doubleQuotes=array('&#8222;','&#8220;');public$singleQuotes=array('&#8218;','&#8216;');private$from,$to;public
function
init(){$pairs=array('#(?<!"|\w)"(?!\ |")(.+)(?<!\ |")"(?!")()#U'=>$this->doubleQuotes[0].'$1'.$this->doubleQuotes[1],'#(?<!\'|\w)\'(?!\ |\')(.+)(?<!\ |\')\'(?!\')()#U<UTF>'=>$this->singleQuotes[0].'$1'.$this->singleQuotes[1],'#(\S|^) ?\.{3}#m'=>'$1&#8230;','#(\d| )-(\d| )#'=>"\$1&#8211;\$2",'#,-#'=>",&#8211;",'#(?<!\d)(\d{1,2}\.) (\d{1,2}\.) (\d\d)#'=>'$1&#160;$2&#160;$3','#(?<!\d)(\d{1,2}\.) (\d{1,2}\.)#'=>'$1&#160;$2','# --- #'=>" &#8212; ",'# -- #'=>" &#8211; ",'# -&gt; #'=>' &#8594; ','# &lt;- #'=>' &#8592; ','# &lt;-&gt; #'=>' &#8596; ','#(\d+)( ?)x\\2(\d+)\\2x\\2(\d+)#'=>'$1&#215;$3&#215;$4','#(\d+)( ?)x\\2(\d+)#'=>'$1&#215;$3','#(?<=\d)x(?= |,|.|$)#m'=>'&#215;','#(\S ?)\(TM\)#i'=>'$1&#8482;','#(\S ?)\(R\)#i'=>'$1&#174;','#\(C\)( ?\S)#i'=>'&#169;$1','#(\d{1,3}) (\d{3}) (\d{3}) (\d{3})#'=>'$1&#160;$2&#160;$3&#160;$4','#(\d{1,3}) (\d{3}) (\d{3})#'=>'$1&#160;$2&#160;$3','#(\d{1,3}) (\d{3})#'=>'$1&#160;$2','#(?<=^| |\.|,|-|\+)(\d+)([:HASHSOFT:]*) ([:HASHSOFT:]*)([:CHAR:])#m<UTF>'=>'$1$2&#160;$3$4','#(?<=^|[^0-9:CHAR:])([:HASHSOFT:]*)([ksvzouiKSVZOUIA])([:HASHSOFT:]*) ([:HASHSOFT:]*)([0-9:CHAR:])#m<UTF>'=>'$1$2$3&#160;$4$5',);$this->from=$this->to=array();foreach($pairs
as$pattern=>$replacement){$this->from[]=$this->texy->translatePattern($pattern);$this->to[]=$replacement;}}public
function
linePostProcess($text){if(!$this->allowed)return$text;return
preg_replace($this->from,$this->to,$text);}} 

class
TexyQuoteModule
extends
TexyModule{public$handler;public$allowed;public
function
__construct($texy){parent::__construct($texy);$this->allowed=(object)NULL;$this->allowed->line=TRUE;$this->allowed->block=TRUE;}public
function
init(){if($this->allowed->block)$this->texy->registerBlockPattern($this,'processBlock','#^(?:<MODIFIER_H>\n)?\>(\ +|:)(\S.*)$#mU');if($this->allowed->line)$this->texy->registerLinePattern($this,'processLine','#(?<!\>)(\>\>)(?!\ |\>)(.+)<MODIFIER>?(?<!\ |\<)\<\<(?!\<)<LINK>??()#U','q');}public
function
processLine($parser,$matches,$tag){list(,$mMark,$mContent,$mMod1,$mMod2,$mMod3,$mLink)=$matches;$texy=$this->texy;$el=new
TexyQuoteElement($texy);$el->modifier->setProperties($mMod1,$mMod2,$mMod3);if($mLink)$el->cite->set($mLink);if($this->handler)if(call_user_func_array($this->handler,array($el))===FALSE)return'';return$parser->element->appendChild($el,$mContent);}public
function
processBlock($parser,$matches){list(,$mMod1,$mMod2,$mMod3,$mMod4,$mSpaces,$mContent)=$matches;$texy=$this->texy;$el=new
TexyBlockQuoteElement($texy);$el->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);$content='';$linkTarget='';$spaces='';do{if($mSpaces==':')$linkTarget=trim($mContent);else{if($spaces==='')$spaces=max(1,strlen($mSpaces));$content.=$mContent.TEXY_NEWLINE;}if(!$parser->receiveNext("#^>(?:|(\ {1,$spaces}|:)(.*))()$#mA",$matches))break;list(,$mSpaces,$mContent)=$matches;}while(TRUE);if($linkTarget){$elx=new
TexyLinkElement($this->texy);$elx->setLinkRaw($linkTarget);$el->cite->set($elx->link->asURL());}$el->parse($content);if($this->handler)if(call_user_func_array($this->handler,array($el))===FALSE)return;$parser->element->appendChild($el);}}class
TexyBlockQuoteElement
extends
TexyBlockElement{public$tag='blockquote';public$cite;public
function
__construct($texy){parent::__construct($texy);$this->cite=new
TexyUrl($texy);}protected
function
generateTags(&$tags){parent::generateTags($tags);$tags[$this->tag]['cite']=$this->cite->asURL();}}class
TexyQuoteElement
extends
TexyInlineTagElement{public$tag='q';public$cite;public
function
__construct($texy){parent::__construct($texy);$this->cite=new
TexyUrl($texy);}protected
function
generateTags(&$tags){parent::generateTags($tags);$tags[$this->tag]['cite']=$this->cite->asURL();}} 

class
TexyScriptModule
extends
TexyModule{public$handler;public
function
init(){$this->texy->registerLinePattern($this,'processLine','#\{\{([^:HASH:]+)\}\}()#U');}public
function
processLine($parser,$matches,$tag){list(,$mContent)=$matches;$identifier=trim($mContent);if($identifier==='')return;$args=NULL;if(preg_match('#^([a-z_][a-z0-9_]*)\s*\(([^()]*)\)$#i',$identifier,$matches)){$identifier=$matches[1];$args=explode(',',$matches[2]);array_walk($args,'trim');}$el=new
TexyScriptElement($this->texy);do{if($this->handler===NULL)break;if(is_object($this->handler)){if($args===NULL&&isset($this->handler->$identifier)){$el->setContent($this->handler->$identifier);break;}if(is_array($args)&&is_callable(array($this->handler,$identifier))){array_unshift($args,NULL);$args[0]=$el;call_user_func_array(array($this->handler,$identifier),$args);break;}break;}if(is_callable($this->handler))call_user_func_array($this->handler,array($el,$identifier,$args));}while(0);return$parser->element->appendChild($el);}public
function
defaultHandler($element,$identifier,$args){if($args)$identifier.='('.implode(',',$args).')';$element->setContent('<texy:script content="'.TexyHtml::htmlChars($identifier,TRUE).'" />',TRUE);}}class
TexyScriptElement
extends
TexyTextualElement{} 

class
TexyTableModule
extends
TexyModule{public$handler;public$oddClass='';public$evenClass='';private$isHead;private$colModifier;private$last;private$row;public
function
init(){$this->texy->registerBlockPattern($this,'processBlock','#^(?:<MODIFIER_HV>\n)?'.'\|.*()$#mU');}public
function
processBlock($parser,$matches){list(,$mMod1,$mMod2,$mMod3,$mMod4,$mMod5)=$matches;$texy=$this->texy;$el=new
TexyTableElement($texy);$el->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4,$mMod5);$parser->moveBackward();if($parser->receiveNext('#^\|(\#|\=){2,}(?!\\1)(.*)\\1*\|? *'.TEXY_PATTERN_MODIFIER_H.'?()$#Um',$matches)){list(,$mChar,$mContent,$mMod1,$mMod2,$mMod3,$mMod4)=$matches;$el->caption=new
TexyTextualElement($texy);$el->caption->tag='caption';$el->caption->parse($mContent);$el->caption->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4);}$this->isHead=FALSE;$this->colModifier=array();$this->last=array();$this->row=0;while(TRUE){if($parser->receiveNext('#^\|\-{3,}$#Um',$matches)){$this->isHead=!$this->isHead;continue;}if($elRow=$this->processRow($parser)){if($this->handler)if(call_user_func_array($this->handler,array($elRow,'row'))===FALSE)continue;$el->appendChild($elRow);$this->row++;continue;}break;}if($this->handler)if(call_user_func_array($this->handler,array($el,'table'))===FALSE)return;$parser->element->appendChild($el);}protected
function
processRow($parser){$texy=$this->texy;if(!$parser->receiveNext('#^\|(.*)(?:|\|\ *'.TEXY_PATTERN_MODIFIER_HV.'?)()$#U',$matches)){return
FALSE;}list(,$mContent,$mMod1,$mMod2,$mMod3,$mMod4,$mMod5)=$matches;$elRow=new
TexyTableRowElement($this->texy);$elRow->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4,$mMod5);if($this->row
%
2==0){if($this->oddClass)$elRow->modifier->classes[]=$this->oddClass;}else{if($this->evenClass)$elRow->modifier->classes[]=$this->evenClass;}$col=0;$elField=NULL;foreach(explode('|',$mContent)as$field){if(($field=='')&&$elField){$elField->colSpan++;unset($this->last[$col]);$col++;continue;}$field=rtrim($field);if($field=='^'){if(isset($this->last[$col])){$this->last[$col]->rowSpan++;$col+=$this->last[$col]->colSpan;continue;}}if(!preg_match('#(\*??)\ *'.TEXY_PATTERN_MODIFIER_HV.'??(.*)'.TEXY_PATTERN_MODIFIER_HV.'?()$#AU',$field,$matches))continue;list(,$mHead,$mModCol1,$mModCol2,$mModCol3,$mModCol4,$mModCol5,$mContent,$mMod1,$mMod2,$mMod3,$mMod4,$mMod5)=$matches;if($mModCol1||$mModCol2||$mModCol3||$mModCol4||$mModCol5){$this->colModifier[$col]=new
TexyModifier($this->texy);$this->colModifier[$col]->setProperties($mModCol1,$mModCol2,$mModCol3,$mModCol4,$mModCol5);}$elField=new
TexyTableFieldElement($texy);$elField->isHead=($this->isHead||($mHead=='*'));if(isset($this->colModifier[$col]))$elField->modifier->copyFrom($this->colModifier[$col]);$elField->modifier->setProperties($mMod1,$mMod2,$mMod3,$mMod4,$mMod5);$elField->parse($mContent);$elRow->appendChild($elField);$this->last[$col]=$elField;$col++;}return$elRow;}}class
TexyTableElement
extends
TexyBlockElement{public$tag='table';public$caption;protected
function
generateContent(){$html=parent::generateContent();if($this->caption)$html=$this->caption->toHtml().$html;return$html;}}class
TexyTableRowElement
extends
TexyBlockElement{public$tag='tr';}class
TexyTableFieldElement
extends
TexyTextualElement{public$colSpan=1;public$rowSpan=1;public$isHead;protected
function
generateTags(&$tags){$this->tag=$this->isHead?'th':'td';parent::generateTags($tags);if($this->colSpan<>1)$tags[$this->tag]['colspan']=(int)$this->colSpan;if($this->rowSpan<>1)$tags[$this->tag]['rowspan']=(int)$this->rowSpan;}protected
function
generateContent(){$html=parent::generateContent();return$html==''?'&#160;':$html;}} 

class
TexySmiliesModule
extends
TexyModule{public$handler;public$allowed=FALSE;public$icons=array(':-)'=>'smile.gif',':-('=>'sad.gif',';-)'=>'wink.gif',':-D'=>'biggrin.gif','8-O'=>'eek.gif','8-)'=>'cool.gif',':-?'=>'confused.gif',':-x'=>'mad.gif',':-P'=>'razz.gif',':-|'=>'neutral.gif',);public$root='images/smilies/';public$class='';public
function
init(){if($this->allowed){krsort($this->icons);$pattern=array();foreach($this->icons
as$key=>$value)$pattern[]=preg_quote($key,'#').'+';$crazyRE='#(?<=^|[\\x00-\\x20])('.implode('|',$pattern).')#';$this->texy->registerLinePattern($this,'processLine',$crazyRE);}}public
function
processLine($parser,$matches){$match=$matches[0];$texy=$this->texy;$el=new
TexyImageElement($texy);$el->modifier->title=$match;$el->modifier->classes[]=$this->class;foreach($this->icons
as$key=>$value)if(substr($match,0,strlen($key))==$key){$el->image->set($value,$this->root,TRUE);break;}if($this->handler)if(call_user_func_array($this->handler,array($el))===FALSE)return'';return$parser->element->appendChild($el);}} 
class
Texy{const
ALL=TRUE;const
NONE=FALSE;public$utf=FALSE;public$tabWidth=8;public$allowedClasses=Texy::ALL;public$allowedStyles=Texy::ALL;public$allowedTags;public$obfuscateEmail=TRUE;private$DOM;public$summary;public$styleSheet='';public$mergeLines=TRUE;public$referenceHandler;public$scriptModule,$htmlModule,$imageModule,$linkModule,$phraseModule,$smiliesModule,$blockModule,$headingModule,$horizLineModule,$quoteModule,$listModule,$definitionListModule,$tableModule,$imageDescModule,$genericBlockModule,$quickCorrectModule,$longWordsModule,$formatterModule;private$linePatterns=array();private$blockPatterns=array();private$modules;private$references=array();public
function
__construct(){$this->summary['images']=array();$this->summary['links']=array();$this->summary['preload']=array();$this->allowedTags=TexyHtml::$valid;$this->loadModules();}protected
function
loadModules(){$this->scriptModule=new
TexyScriptModule($this);$this->htmlModule=new
TexyHtmlModule($this);$this->imageModule=new
TexyImageModule($this);$this->linkModule=new
TexyLinkModule($this);$this->phraseModule=new
TexyPhraseModule($this);$this->smiliesModule=new
TexySmiliesModule($this);$this->blockModule=new
TexyBlockModule($this);$this->headingModule=new
TexyHeadingModule($this);$this->horizLineModule=new
TexyHorizLineModule($this);$this->quoteModule=new
TexyQuoteModule($this);$this->listModule=new
TexyListModule($this);$this->definitionListModule=new
TexyDefinitionListModule($this);$this->tableModule=new
TexyTableModule($this);$this->imageDescModule=new
TexyImageDescModule($this);$this->genericBlockModule=new
TexyGenericBlockModule($this);$this->quickCorrectModule=new
TexyQuickCorrectModule($this);$this->longWordsModule=new
TexyLongWordsModule($this);$this->formatterModule=new
TexyFormatterModule($this);}public
function
registerModule($module){$this->modules[]=$module;}public
function
registerLinePattern($module,$method,$pattern,$user_args=NULL){$this->linePatterns[]=array('handler'=>array($module,$method),'pattern'=>$this->translatePattern($pattern),'user'=>$user_args);}public
function
registerBlockPattern($module,$method,$pattern,$user_args=NULL){$this->blockPatterns[]=array('handler'=>array($module,$method),'pattern'=>$this->translatePattern($pattern).'m','user'=>$user_args);}public
function
getLinePatterns(){return$this->linePatterns;}public
function
getBlockPatterns(){return$this->blockPatterns;}protected
function
init(){$this->cache=array();$this->linePatterns=array();$this->blockPatterns=array();if(!$this->modules)die('Texy: No modules installed');foreach($this->modules
as$module)$module->init();}public
function
process($text,$singleLine=FALSE){if($singleLine)$this->parseLine($text);else$this->parse($text);return$this->DOM->toHtml();}public
function
parse($text){$this->init();$this->DOM=new
TexyDom($this);$this->DOM->parse($text);}public
function
parseLine($text){$this->init();$this->DOM=new
TexyDomLine($this);$this->DOM->parse($text);}public
function
toHtml(){return$this->DOM->toHtml();}public
function
toText(){$saveLineWrap=$this->formatterModule->lineWrap;$this->formatterModule->lineWrap=FALSE;$text=$this->toHtml();$this->formatterModule->lineWrap=$saveLineWrap;$text=preg_replace('#<(script|style)(.*)</\\1>#Uis','',$text);$text=strip_tags($text);$text=preg_replace('#\n\s*\n\s*\n[\n\s]*\n#',"\n\n",$text);if((int)PHP_VERSION>4&&$this->utf){$text=html_entity_decode($text,ENT_QUOTES,'UTF-8');}else{$text=strtr($text,array('&amp;'=>'&#38;','&quot;'=>'&#34;','&lt;'=>'&#60;','&gt;'=>'&#62;'));$text=preg_replace_callback('#&(\\#x[0-9a-fA-F]+|\\#[0-9]+);#',array($this,'_entityCallback'),$text);}$text=strtr($text,array($this->utf?"\xC2\xAD":"\xAD"=>'',$this->utf?"\xC2\xA0":"\xA0"=>' ',));return$text;}private
function
_entityCallback($matches){list(,$entity)=$matches;$ord=($entity{1}=='x')?hexdec(substr($entity,2)):(int)substr($entity,1);if($ord<128)return
chr($ord);if($this->utf){if($ord<2048)return
chr(($ord>>6)+192).chr(($ord&63)+128);if($ord<65536)return
chr(($ord>>12)+224).chr((($ord>>6)&63)+128).chr(($ord&63)+128);if($ord<2097152)return
chr(($ord>>18)+240).chr((($ord>>12)&63)+128).chr((($ord>>6)&63)+128).chr(($ord&63)+128);return$match;}if(function_exists('iconv')){return(string)iconv('UCS-2','WINDOWS-1250//TRANSLIT',pack('n',$ord));}return'?';}public
function
safeMode(){$this->allowedClasses=Texy::NONE;$this->allowedStyles=Texy::NONE;$this->htmlModule->safeMode();$this->imageModule->allowed=FALSE;$this->linkModule->forceNoFollow=TRUE;}public
function
trustMode(){$this->allowedClasses=Texy::ALL;$this->allowedStyles=Texy::ALL;$this->htmlModule->trustMode();$this->imageModule->allowed=TRUE;$this->linkModule->forceNoFollow=FALSE;}static
public
function
freezeSpaces($s){return
strtr($s," \t\r\n","\x15\x16\x17\x18");}static
public
function
unfreezeSpaces($s){return
strtr($s,"\x15\x16\x17\x18"," \t\r\n");}static
public
function
wash($text){return
preg_replace('#[\x15-\x1F]+#','',$text);}static
public
function
isHashOpening($hash){return$hash{1}=="\x1F";}public
function
addReference($name,$obj){$name=strtolower($name);$this->references[$name]=$obj;}function
getReference($name){$lowName=strtolower($name);if(isset($this->references[$lowName]))return$this->references[$lowName];if($this->referenceHandler)return
call_user_func_array($this->referenceHandler,array($name,$this));return
FALSE;}private$cache;public
function
translatePattern($re){if(isset($this->cache[$re]))return$this->cache[$re];return$this->cache[$re]=strtr($re,array('<MODIFIER_HV>'=>TEXY_PATTERN_MODIFIER_HV,'<MODIFIER_H>'=>TEXY_PATTERN_MODIFIER_H,'<MODIFIER>'=>TEXY_PATTERN_MODIFIER,'<LINK>'=>TEXY_PATTERN_LINK,'<UTF>'=>($this->utf?'u':''),':CHAR:'=>($this->utf?TEXY_CHAR_UTF:TEXY_CHAR),':HASH:'=>TEXY_HASH,':HASHSOFT:'=>TEXY_HASH_NC,));}public
function
getModules(){return$this->modules;}public
function
free(){foreach(array_keys(get_object_vars($this))as$key)$this->$key=NULL;}function
__set($nm,$val){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}function
__get($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__unset($nm){$c=get_class($this);trigger_error("Undefined property '$c::$$nm'",E_USER_ERROR);}private
function
__isset($nm){return
FALSE;}}?>