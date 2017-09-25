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

	"accepted"             => ":attribute muss akzeptiert werden.",
	"active_url"           => ":attribute ist keine gültige URL.",
	"after"                => ":attribute muss ein Datum nach :date sein.",
	"alpha"                => ":attribute darf nur Buchstaben enthalten.",
	"alpha_dash"           => ":attribute darf nur Buchstaben, Zahlen oder Striche enthalten.",
	"alpha_num"            => ":attribute darf nur Buchstaben oder Zahlen enthalten.",
	"array"                => ":attribute muss ein Bereich sein.",
	"before"               => ":attribute muss ein Datum vor :date sein.",
	"between"              => array(
		"numeric" => ":attribute muss einen Wert zwischen :min und :max. haben.",
		"file"    => ":attribute muss einen Wert zwischen :min und :max kilobytes sein.",
		"string"  => ":attribute muss einen Wert zwischen :min und :max Zeichen sein.",
		"array"   => ":attribute muss zwischen :min und :max liegen.",
	),
	"confirmed"            => ":attribute Bestätigung stimmt nicht überein.",
	"date"                 => ":attribute ist kein gültiges Datum.",
	"date_format"          => ":attribute stimmt nicht mit dem Format :format überrein.",
	"different"            => ":attribute und :other müssen unterschiedlich sein.",
	"digits"               => ":attribute müssen :digits Ziffern sein.",
	"digits_between"       => ":attribute muss zwischen :min und :max Ziffern liegen.",
	"email"                => ":attribute muss eine gültige E-Mail Adresse sein.",
	"exists"               => "Das selektierte :attribute ist ungültig.",
	"image"                => ":attribute muss ein Bild sein.",
	"in"                   => "Das selektierte :attribute ist ungültig.",
	"integer"              => ":attribute muss eine ganze Zahl sein.",
	"ip"                   => "s:attribute muss eine gültige IP Adresse sein.",
	"max"                  => array(
		"numeric" => ":attribute darf nicht grösser sein als :max.",
		"file"    => ":attribute darf nicht grösser sein als :max Kilobytes.",
		"string"  => ":attribute darf nicht grösser sein als :max Zeichen.",
		"array"   => ":attribute darf nicht mehr sein als :max Stück.",
	),
	"mimes"                => ":attribute muss eine Datei vom Type: :values sein.",
	"min"                  => array(
		"numeric" => ":attribute darf nicht kleiner sein als :min.",
		"file"    => ":attribute darf nicht kleiner sein als :min Kilobytes.",
		"string"  => ":attribute darf nicht kleiner sein als :min Zeichen.",
		"array"   => ":attribute darf nicht kleiner sein als :min Stück.",
	),
	"not_in"               => "Das gewählte :attribute ist ungültig.",
	"numeric"              => "Das :attribute muss eine Zahl sein.",
	"regex"                => "Das :attribute format ist ungültig.",
	"required"             => "Das :attribute Feld ist erforderlich.",
	"required_if"          => "Das :attribute Feld ist erforderlich wenn :other ist :value.",
	"required_with"        => "Das :attribute Feld ist erforderlich wenn :values ist vorhanden.",
	"required_with_all"    => "Das :attribute Feld ist erforderlich wenn :values ist vorhanden.",
	"required_without"     => "Das :attribute Feld ist erforderlich wenn :values ist nicht vorhanden.",
	"required_without_all" => "Das :attribute Feld ist erforderlich wenn keine der :values vorhanden sind.",
	"same"                 => "Das :attribute und :other muss gleich sein.",
	"size"                 => array(
		"numeric" => ":attribute muss :size sein.",
		"file"    => ":attribute muss :size Kilobytes gross sein.",
		"string"  => ":attribute muss aus :size Zeichen bestehen.",
		"array"   => ":attribute muss :size Stück beinhalten.",
	),
	"unique"               => ":attribute ist schon verwendet worden.",
	"url"                  => ":attribute format ist ungültig.",

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
