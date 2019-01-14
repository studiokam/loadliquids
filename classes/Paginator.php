<?php
 
class Paginator {
    
    //declare all internal (private) variables, only accessbile within this class
    private $_conn;
    private $_limit; //records (rows) to show per page
    private $_page; //current page
    private $_query;
    private $_sort;
    private $_search_args;
    private $_total;
    private $_row_start;
    
    //constructor method is called automatically when object is instantiated with new keyword
    public function __construct( $query ) 
    {


    	try {
    		$conn = new PDO(
    			'mysql:host=' . Config::get('mysql/host') . ';
    			dbname=' . Config::get('mysql/db'), 
    			Config::get('mysql/username'), 
    			Config::get('mysql/password'));
    	} catch (PDOException $e) {
    		die($e->getMessage());
    	}
     
        //$this-> variables become available anywhere within THIS class
        $this->_conn = $conn; //mysql connection resource
        $this->_query = $query; //mysql query string
     
        $rs = $this->_conn->query($this->_query);
    	$this->_total = $rs->rowCount();

    }

    
    //LIMIT DATA
    //all it does is limits the data returned and returns everything as $result object
    public function getData( $limit = 10, $page = 1, $search_args, $sort_args ) { //set default argument values
     
        $this->_limit = $limit;
        $this->_sort = $sort_args;
        $this->_search_args = $search_args;
        //echo $this->_limit.'<br>';
        $this->_page = $page;
        //echo $this->_page.'<br>';

        //no limiting necessary, use query as it is
        if ( $this->_limit == 'all' ) {
            $query = $this->_query;
        } 
        
        else {
            //echo ( ( $this->_page - 1 ) * $this->_limit );die;
            //create the query, limiting records from page, to limit
            $this->_row_start = ( ( $this->_page - 1 ) * $this->_limit );
            $query = $this->_query .
                    //add to original query: ( minus one because of the way SQL works )
                    " LIMIT {$this->_row_start}, $this->_limit";
        }
        
        //echo $query;die;
        
        $rs = $this->_conn->query( $query ) or die($this->_conn->error);

        while ( $row = $rs->fetch( PDO::FETCH_ASSOC ) ) {
            //store this array in $result->data below
            $results[]  = $row; 
        }
        //echo '<pre>';
        //print_r($results);die;

        //return data as object, new stdClass() creates new empty object
        $result         = new stdClass();
        $result->page   = $this->_page;
        $result->limit  = $this->_limit;
        $result->total  = $this->_total;
        $result->data   = $results; //$result->data = array

        //print_r($result);die;
        return $result; //object
    }
    
    //PRINT LINKS
    public function createLinks( $links, $list_class ) 
    {
        //return empty result string, no links necessary
        if ( $this->_limit == 'all' ) {
            return '';
        }

        //get the last page number
        $last = ceil( $this->_total / $this->_limit );
        
        //calculate start of range for link printing
        $start = ( ( $this->_page - $links ) > 0 ) ? $this->_page - $links : 1;
        
        //calculate end of range for link printing
        $end = ( ( $this->_page + $links ) < $last ) ? $this->_page + $links : $last;

        //debugging
        // echo '$total: '.$this->_total.' | '; //total rows
        // echo '$row_start: '.$this->_row_start.' | '; //total rows
        // echo '$limit: '.$this->_limit.' | '; //total rows per query
        // echo '$start: '.$start.' | '; //start printing links from
        // echo '$end: '.$end.' | '; //end printing links at
        // echo '$last: '.$last.' | '; //last page
        // echo '$page: '.$this->_page.' | '; //current page
        // echo '$links: '.$links.' <br /> '; //links 


        //ul boot strap class - "pagination pagination-sm"
        $html = '<ul class="' . $list_class . '">';

        $class = ( $this->_page == 1 ) ? "disabled" : ""; //disable previous page link <<<
        
        //create the links and pass limit and page as $_GET parameters

        //$this->_page - 1 = previous page (<<< link )
        $previous_page = ( $this->_page == 1 ) ? 
        '<li class="page-item no-border ' . $class . '"><a class="page-link" " href="">&laquo;</a></li>' : //remove link from previous button
        '<li class="page-item no-border ' . $class . '"><a class="page-link" href="?limit=' . $this->_limit . '&page=' . ( $this->_page - 1 ) . '">&laquo;</a></li>';

        $html .= $previous_page;

        if ( $start > 1 ) { //print ... before (previous <<< link)
            $html .= '<li class="page-item no-border"><a class="page-link" href="?limit=' . $this->_limit . '&page=1">1</a></li>'; //print first page link
            $html .= '<li class="page-item no-border disabled"><a class="page-link" " href="">...</a></li>'; //print 3 dots if not on first page
        }

        //print all the numbered page links
        for ( $i = $start ; $i <= $end; $i++ ) {
            $class = ( $this->_page == $i ) ? "active" : ""; //highlight current page
            $html .= '<li class="page-item no-border ' . $class . '"><a class="page-link" href="?'. $this->_sort .'&'. $this->_search_args .'&limit=' . $this->_limit . '&page=' . $i . '">' . $i . '</a></li>';
        }

        if ( $end < $last ) { //print ... before next page (>>> link)
            $html .= '<li class="page-item no-border disabled"><a class="page-link" " href="">...</a></li>'; //print 3 dots if not on last page
            $html .= '<li class="page-item no-border"><a class="page-link" href="?limit=' . $this->_limit . '&page=' . $last . '">' . $last . '</a></li>'; //print last page link
        }

        $class = ( $this->_page == $last ) ? "disabled" : ""; //disable (>>> next page link)
        
        //$this->_page + 1 = next page (>>> link)
        $next_page = ( $this->_page == $last) ? 
        '<li class="page-item no-border ' . $class . '"><a class="page-link" href="">&raquo;</a></li>' : //remove link from next button
        '<li class="page-item no-border ' . $class . '"><a class="page-link" href="?limit=' . $this->_limit . '&page=' . ( $this->_page + 1 ) . '">&raquo;</a></li>';

        $html .= $next_page;
        $html .= '</ul>';
        
        return $html;
    }

    // generowanie linków do sortowania
    // $limit, $page, $search - podawanie wartości jakie występuj w url (sprawdzenie czy sa, jeśli tak to echo)
    // $dane - tablica z wartosciami jakie maja byc drukowane
    public function createSortLinks($limit, $page, $search, $dane) {
        foreach ($dane as $key => $value) {
            echo '<a class="dropdown-item" href="'.$key.(isset($_GET[$limit]) ? '&limit='.$_GET[$limit] : '').(isset($_GET[$page]) ? '&page='.$_GET[$page] : '').(isset($_GET[$search]) ? '&search='.$_GET[$search] : '') .'">'.$value.'</a>';
        }
    }

    public function createLimitLinks($link, $sort, $search, $dane) {
        foreach ($dane as $value) {
            echo '<a class="dropdown-item" href="'.$link.$value.(isset($_GET[$sort]) ? '&sort='.$_GET[$sort] : ''), (isset($_GET[$search]) ? '&search='.$_GET[$search] : '') .'">' . $value . '</a>';
        }
    }

    public function total() {
        return $this->_total;
    }
}
?>
