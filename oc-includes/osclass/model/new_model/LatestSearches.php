<?php

    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */

    /**
     * LastestSearches DAO
     */
    class LastestSearches extends DAO
    {
        /**
         *
         * @var type 
         */
        private static $instance ;

        public static function newInstance()
        {
            if( !self::$instance instanceof self ) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        /**
         * 
         */
        function __construct()
        {
            parent::__construct();
            $this->set_table_name('t_latest_searches') ;
            $array_fields = array(
                'd_date',
                's_search'
            );
            $this->set_fields($array_fields) ;
        }
        
        /**
         * Get last searches, given a limit.
         * 
         * @param integer $limit
         * @return array
         */
        function getSearches($limit = 20)
        {
            $this->dao->select('d_date, s_search, COUNT(s_search) as i_total') ;
            $this->dao->from($this->table_name) ;
            $this->dao->group_by('s_search') ;
            $this->dao->order_by('d_date', 'DESC') ;
            $this->dao->limit($limit) ;
            $result = $this->dao->get() ;
            
            if( $result == false ) { 
                return false;
            } else {
                return $result->result() ;
            }
        }
        
        /**
         * Get last searches, given since time.
         * 
         * @param integer $time
         * @return array
         */
        function getSearchesByDate($time = null) 
        {
            if($time==null) { $time = time() - (7*24*3600); };
            
            $this->dao->select('d_date, s_search, COUNT(s_search) as i_total') ;
            $this->dao->from($this->table_name) ;
            $this->dao->where('d_date', date('Y-m-d H:i:s', $time)) ;
            $this->dao->group_by('s_search') ;
            $this->dao->order_by('d_date', 'DESC') ;
            $this->dao->limit($limit) ;
            $result = $this->dao->get() ;
            
            if( $result == false ) { 
                return false;
            } else {
                return $result->result() ;
            }
        }

        /**
         * Purge all searches by date.
         * 
         * @param string $date
         * @return boolean
         */
        function purgeDate($date = null) 
        {
            if($date!=null) {
                return $this->dao->delete($this->table_name, array('d_date <= '.$date));
            } else {
                return false;
            }
        }
        
        /**
         * Purge n last searches.
         *
         * @param integer $number
         * @return boolean
         */
        public function purgeNumber($number = null) {
            if($number!=null) {
                $this->dao->select('d_date') ;
                $this->dao->from($this->table_name) ; 
                $this->dao->group_by('s_search') ;
                $this->dao->order_by('d_date', 'DESC') ;
                $this->dao->limit($number, 1) ;
                $result = $this->dao->get() ;
                $last= $result->row();
                
                return $this->dao->delete($this->table_name, array('d_date <= '.$last['d_date']));
            } else {
                return false;
            }
        }
    }
?>