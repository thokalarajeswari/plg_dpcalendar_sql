<?php
/**
* @package     A main package name
* @subpackage  dpcalendar_sql
*
* @copyright   A copyright
* @license     A "Slug" license name e.g. GPL2
*/

defined('_JEXEC') or die;

JLoader::import('components.com_dpcalendar.libraries.dpcalendar.syncplugin', JPATH_ADMINISTRATOR);
if (! class_exists('DPCalendarSyncPlugin'))
{
    return;
}

class PlgDPCalendarDPCalendar_SQL extends DPCalendarSyncPlugin
{
    
}
?>