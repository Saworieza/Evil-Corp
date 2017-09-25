<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"             => "O campo :attribute tem de ser aceite.",
	"active_url"           => "O campo :attribute não é um URL válido.",
	"after"                => "O campo :attribute tem de ser uma data após :date.",
	"alpha"                => "O campo :attribute só pode conter letras.",
	"alpha_dash"           => "O campo :attribute só pode conter letras, números, e traços.",
	"alpha_num"            => "O campo :attribute só pode conter letras e números.",
	"array"                => "O campo :attribute tem de ser um array.",
	"before"               => "O campo :attribute tem de ser uma data antes :date.",
	"between"              => array(
		"numeric" => "O campo :attribute tem de ser entre :min e :max.",
		"file"    => "O campo :attribute tem de ser entre :min e :max kilobytes.",
		"string"  => "O campo :attribute tem de ser entre :min e :max caractéres.",
		"array"   => "O campo :attribute tem de ser entre :min e :max items.",
	),
	"confirmed"            => "O campo :attribute confirmação não está igual.",
	"date"                 => "O campo :attribute não é uma data válida.",
	"date_format"          => "O campo :attribute não coincide com o formato :format.",
	"different"            => "O campo :attribute e :other têm de ser diferentes.",
	"digits"               => "O campo :attribute tem de ser :digits digitos.",
	"digits_between"       => "O campo :attribute tem de ser entre :min e :max digitos.",
	"email"                => "O campo :attribute tem de ser um email válido.",
	"exists"               => "O campo :attribute seleccionado é inválido.",
	"image"                => "O campo :attribute tem de ser uma imagem.",
	"in"                   => "O campo :attribute seleccionada é inválida.",
	"integer"              => "O campo :attribute tem de ser um número inteiro.",
	"ip"                   => "O campo :attribute tem de ser um endereço IP válido.",
	"max"                  => array(
		"numeric" => "O campo :attribute não pode ser maior que :max.",
		"file"    => "O campo :attribute não pode ser maior que :max kilobytes.",
		"string"  => "O campo :attribute não pode ser maior :max caracteres.",
		"array"   => "O campo :attribute não pode conter mais que :max items.",
	),
	"mimes"                => "O campo :attribute tem de ser um ficheiro do tipo: :values.",
	"min"                  => array(
		"numeric" => "O campo :attribute tem de ser pelo menos :min.",
		"file"    => "O campo :attribute tem de ser pelo menos :min kilobytes.",
		"string"  => "O campo :attribute tem de ser pelo menos :min caractéres.",
		"array"   => "O campo :attribute tem de ter pelo menos :min items.",
	),
	"not_in"               => "O campo :attribute seleccionado é inválido.",
	"numeric"              => "O campo :attribute tem de ser um número.",
	"regex"                => "O formato do campo :attribute é inválido.",
	"required"             => "O campo :attribute é obrigatório.",
	"required_if"          => "O campo :attribute é necessário quando :other é :value.",
	"required_with"        => "O campo :attribute é necessário quando :values existe.",
	"required_with_all"    => "O campo :attribute é necessário quando :values existe.",
	"required_without"     => "O campo :attribute é necessário quando :values não existe.",
	"required_without_all" => "O campo :attribute é necessário quando nenhum dos :values existem.",
	"same"                 => "O campo :attribute e :other têm de ser iguais.",
	"size"                 => array(
		"numeric" => "O campo :attribute tem de ser :size.",
		"file"    => "O campo :attribute tem de ser :size kilobytes.",
		"string"  => "O campo :attribute tem de ser :size caratéres.",
		"array"   => "O campo :attribute tem de conter :size items.",
	),
	"unique"               => "O campo :attribute já foi tomado.",
	"url"                  => "O formato do campo :attribute é inválido.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(
		'attribute-name' => array(
			'rule-name' => 'custom-message',
		),
	),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(),

);
