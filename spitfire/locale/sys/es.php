<?php namespace spitfire\locale\sys;

use spitfire\locale\Locale;

class Es extends Locale
{
	public $comment_count   = Array('Sin comentarios', 'Un comentario', '%s comentarios');
	
	public $select_pick     = 'Elegir';
	
	public $secondsago      = Array('Ahora mismo', 'Hace un segundo', 'Hace %s segundos');
	public $minutesago      = Array('Hace menos de un minuto', 'Hace un minuto', 'Hace %s minutos');
	public $hoursago        = Array('Hace menos de una hora', 'Hace una hora', 'Hace %s horas');
	public $daysago         = Array('Hace menos de un día', 'Hace un día', 'Hace %s días');
	public $weeksago        = Array('Hace menos de una semana', 'Hace una semana', 'Hace %s semanas');
	public $monthssago      = Array('Hace menos de un mes', 'Hace un mes', 'Hace %s meses');
	public $yearsago        = Array('Hace menos de un año', 'Hace un año', 'Hace %s años');
	
	public $str_too_long    = Array('', 'La cadena debe tener menos de un caracter', 'La cadena debe tener menos de %s caracteres');
	public $str_too_short   = Array('', 'La cadena debe tener más de un caracter', 'La cadena debe tener más de %s caracteres');
	public $err_not_numeric = Array('Se exige un dato numérico');
	public $err_field_null  = Array('El campo no puede ser nulo');
}