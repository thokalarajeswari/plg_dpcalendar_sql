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
    protected $identifier = 's';

    protected function getContent ($calendarId, JDate $startDate = null, JDate $endDate = null, JRegistry $options)
    {

        $calendar = $this->getDbCal($calendarId);

        if (empty($calendar))
        {
            return '';
        }
        $params = $calendar->params;

        $option = array();

        $option['driver']   = $params->get('driver');
        $option['host']     = $params->get('host_name');
        $option['user']     = $params->get('user');
        $option['password'] = $params->get('pwd');
        $option['database'] = $params->get('db_name');

        $db = JDatabaseDriver::getInstance( $option );

        $query = $db->getQuery(true);

        $columns=array();

        $columns[]= $params->get('start_date_column');
        $columns[]= $params->get('end_date_column');
        $columns[]= $params->get('all_day_column');
        $columns[]= $params->get('title_column');
        $columns[]= $params->get('description_column');
        $columns[]= $params->get('rrule_column');
        $columns[]= $params->get('location_column');
        $columns[]= $params->get('url_column');
        $columns[]= $params->get('alias_column');
        $columns[]= $params->get('color_column');

        $tableName=$params->get('table_name');

        $conditionClause=$params->get('start_date_column') .' >= \''. $startDate . '\' and '.$params->get('end_date_column').' <= \''. $endDate.'\'';

        $query->select($db->quoteName($columns));
        $query->from($db->quoteName($tableName));
        $query->where(($conditionClause));

        $db->setQuery($query);

        $rows = $db->loadObjectList();

        $text = array();
        $text[] = 'BEGIN:VCALENDAR';

        foreach ($rows as $row){

                $text[] = 'BEGIN:VEVENT';

                $allDayCol=$params->get('all_day_column');
                $allDay =$row->$allDayCol;

                $startDateCol=$params->get('start_date_column');
                $startDate=DPCalendarHelper::getDate($row->$startDateCol);

                if ($allDay)
                {
                        $text[] = 'DTSTART;VALUE=DATE:' . $startDate->format('Ymd');
                }
                else
                {
                        $text[] = 'DTSTART:' . $startDate->format('Ymd\THis\Z');
                }

                $endDateCol=$params->get('end_date_column');
                $endDate=DPCalendarHelper::getDate($row->$endDateCol);

                if ($allDay)
                {
                        $text[] = 'DTEND;VALUE=DATE:' . $endDate->format('Ymd');
                }
                else
                {
                        $text[] = 'DTEND:' . $endDate->format('Ymd\THis\Z');
                }

                $titleCol=$params->get('title_column');
                $title=$row->$titleCol;

                $text[] = 'UID:' . md5($title . 'SQL');
                $text[] = 'SUMMARY:' . $title;

                $descCol= $params->get('description_column');
                $desc=$row->$descCol;

                $text[] = 'DESCRIPTION:' .$desc;

                $rruleCol= $params->get('rrule_column');
                $rrule=$row->$rruleCol;

                $text[] = 'RRULE:' .$rrule;

                $locCol= $params->get('location_column');
                $location=$row->$locCol;

                $text[] = 'LOCATION:' .$location;

                $urlCol= $params->get('url_column');
                $url=$row->$urlCol;

                $text[] = 'X-URL:' .$url;

                $aliasCol= $params->get('alias_column');
                $alias=$row->$aliasCol;

                $text[] = 'X-ALIAS:' .$alias;

                $colorCol= $params->get('color_column');
                $color=$row->$colorCol;

                $text[] = 'X-COLOR:' .$color;

                $text[] = 'END:VEVENT';
        }

        $text[] = 'END:VCALENDAR';

        return $text;

    }
}
?>