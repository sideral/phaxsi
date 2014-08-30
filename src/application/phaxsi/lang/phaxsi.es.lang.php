<?php

	class PhaxsiLang extends Lang{

		/**
		 * Default error messages to display on forms. Useful for testing.
		 */
		public $default_errors = array(
			'required'			=> 'Campo requerido',
			'expression'		=> 'El campo tiene caracteres inválidos',
			'max_size'			=> 'El campo ha excedido el máximo tamaño permitido',
			'min_size'			=> 'El campo no ha alcanzado el mínimo tamaño permitido',
			'max_length'		=> 'Se ha excedido la longitud máxima permitida',
			'min_length'		=> 'El número de caracteres es menor que el requerido',
			'max_value'			=> 'El valor es muy grande',
			'min_value'			=> 'El valor es muy pequeño',
			'callback'			=> 'El campo tiene un valor inválido',
			'database_column'	=> 'El campo tiene un valor inválido',
			'summary'			=> 'Hubo errores en el formulario',
			'array_min_count' => 'Hay menos campos llenos que lo requerido',
			'array_max_count' => 'Hay más campos llenos que lo permitido',
			'array_count'		=> 'El número de campos llenos no es el esperado',
			'array_required_values' => 'Uno o más valores requeridos no está presente',
			'array_required_keys' => 'Uno o más campos requeridos no fue rellenado',
			'extension'			=> 'Este formato de archivo no es aceptado',
			'mime_types'		=> 'Este formato de archivo no es aceptado'
		);

		public $http_messages = array(
			400 => array('Solicitud incorrecta', 'Su navegador envió una solicitud que no pudimos entender.'),
			401 => array('Autorización requerida', 'Este servidor no pudo verificar que usted está autorizado para acceder al documento solicitado.'),
			404 => array('Página no encontrada', 'La URL solicitada no fue encontrada en este servidor.'),
			500 => array('Error interno del servidor', 'Por favor intente de nuevo más tarde.')
		);

	}