<!DOCTYPE html>
<html lang="ru">
	<head>
		<title><?=$this->response->renderTitle()?></title>
		<meta name="descritpion" content="<?=$this->response->renderDescription()?>">
		<meta name="keywords" content="<?=$this->response->renderKeywords()?>">
	</head>
	<body>
		<h2><?=$this->response->renderTitle()?></h2>
		<p><?=$message?></p>
		<?php $this->response->renderStart()?>
		<h4>Проба вложенной буфферизации</h4>
		<?php $this->response->setDescription('Изменим описание внутри вложенного вывода')?>
		<div class="description"><?=$this->response->renderDescription()?></div>
		<div class="keywords"><?=$this->response->renderKeywords()?></div>
		<?=$this->response->renderStop()?>
		<?php $this->response->setKeywords('А ключевые слова после')?>
	</body>
</html>