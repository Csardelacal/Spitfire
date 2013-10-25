<?php namespace spitfire\locale\sys;

use Locale;

class Es extends Locale
{
	public $comment_count   = Array('Sin comentarios', 'Un comentario', '%s comentarios');
	
	public $secondsago      = Array('Ahora mismo', 'Hace un segundo', 'Hace %s segundos');
	public $minutesago      = Array('Hace menos de un minuto', 'Hace un minuto', 'Hace %s minutos');
	public $hoursago        = Array('Hace menos de una hora', 'Hace una hora', 'Hace %s horas');
	public $daysago         = Array('Hace menos de un día', 'Hace un día', 'Hace %s días');
	public $weeksago        = Array('Hace menos de una semana', 'Hace una semana', 'Hace %s semanas');
	public $monthssago      = Array('Hace menos de un mes', 'Hace un mes', 'Hace %s meses');
	public $yearsago        = Array('Hace menos de un año', 'Hace un año', 'Hace %s años');
}