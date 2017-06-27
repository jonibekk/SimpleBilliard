<?php

class RecreatingPartitionForPostsPostShareCirclesPostShareUsers0621 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'recreating_partition_for_posts_post_share_circles_post_share_users_0621';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(),
        'down' => array(),
    );

    /**
     * Before migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function before($direction)
    {
        if ($direction === 'up') {
            // posts table
            $this->db->query("ALTER TABLE posts REMOVE PARTITIONING;");
            $this->db->query("ALTER TABLE posts DROP PRIMARY KEY , ADD PRIMARY KEY (id, created);");
            $this->db->query($this->getQueryForAddPartition('posts', 'created'));
            // post_share_users table
            $this->db->query("ALTER TABLE post_share_users REMOVE PARTITIONING;");
            $this->db->query("ALTER TABLE post_share_users DROP PRIMARY KEY , ADD PRIMARY KEY (id, created);");
            $this->db->query($this->getQueryForAddPartition('post_share_users', 'created'));
            // post_share_circles table
            $this->db->query("ALTER TABLE post_share_circles REMOVE PARTITIONING;");
            $this->db->query("ALTER TABLE post_share_circles DROP PRIMARY KEY , ADD PRIMARY KEY (id, created);");
            $this->db->query($this->getQueryForAddPartition('post_share_circles', 'created'));
        } else {
            // posts table
            $this->db->query("ALTER TABLE posts REMOVE PARTITIONING;");
            $this->db->query("ALTER TABLE posts DROP PRIMARY KEY , ADD PRIMARY KEY (id, modified);");
            $this->db->query($this->getQueryForAddPartition('posts', 'modified'));
            // post_share_users table
            $this->db->query("ALTER TABLE post_share_users REMOVE PARTITIONING;");
            $this->db->query("ALTER TABLE post_share_users DROP PRIMARY KEY , ADD PRIMARY KEY (id, modified);");
            $this->db->query($this->getQueryForAddPartition('posts', 'modified'));
            // post_share_circles table
            $this->db->query("ALTER TABLE post_share_circles REMOVE PARTITIONING;");
            $this->db->query("ALTER TABLE post_share_circles DROP PRIMARY KEY , ADD PRIMARY KEY (id, modified);");
            $this->db->query($this->getQueryForAddPartition('posts', 'modified'));

        }
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function after($direction)
    {
        return true;
    }

    function getQueryForAddPartition($table, $key)
    {
        $query = <<< SQL
ALTER TABLE $table
PARTITION BY RANGE ($key) (        
 PARTITION p201407 VALUES LESS THAN (1406851200) ENGINE = InnoDB,
 PARTITION p201408 VALUES LESS THAN (1409529600) ENGINE = InnoDB,
 PARTITION p201409 VALUES LESS THAN (1412121600) ENGINE = InnoDB,
 PARTITION p201410 VALUES LESS THAN (1414800000) ENGINE = InnoDB,
 PARTITION p201411 VALUES LESS THAN (1417392000) ENGINE = InnoDB,
 PARTITION p201412 VALUES LESS THAN (1420070400) ENGINE = InnoDB,
 PARTITION p201501 VALUES LESS THAN (1422748800) ENGINE = InnoDB,
 PARTITION p201502 VALUES LESS THAN (1425168000) ENGINE = InnoDB,
 PARTITION p201503 VALUES LESS THAN (1427846400) ENGINE = InnoDB,
 PARTITION p201504 VALUES LESS THAN (1430438400) ENGINE = InnoDB,
 PARTITION p201505 VALUES LESS THAN (1433116800) ENGINE = InnoDB,
 PARTITION p201506 VALUES LESS THAN (1435708800) ENGINE = InnoDB,
 PARTITION p201507 VALUES LESS THAN (1438387200) ENGINE = InnoDB,
 PARTITION p201508 VALUES LESS THAN (1441065600) ENGINE = InnoDB,
 PARTITION p201509 VALUES LESS THAN (1443657600) ENGINE = InnoDB,
 PARTITION p201510 VALUES LESS THAN (1446336000) ENGINE = InnoDB,
 PARTITION p201511 VALUES LESS THAN (1448928000) ENGINE = InnoDB,
 PARTITION p201512 VALUES LESS THAN (1451606400) ENGINE = InnoDB,
 PARTITION p201601 VALUES LESS THAN (1454284800) ENGINE = InnoDB,
 PARTITION p201602 VALUES LESS THAN (1456790400) ENGINE = InnoDB,
 PARTITION p201603 VALUES LESS THAN (1459468800) ENGINE = InnoDB,
 PARTITION p201604 VALUES LESS THAN (1462060800) ENGINE = InnoDB,
 PARTITION p201605 VALUES LESS THAN (1464739200) ENGINE = InnoDB,
 PARTITION p201606 VALUES LESS THAN (1467331200) ENGINE = InnoDB,
 PARTITION p201607 VALUES LESS THAN (1470009600) ENGINE = InnoDB,
 PARTITION p201608 VALUES LESS THAN (1472688000) ENGINE = InnoDB,
 PARTITION p201609 VALUES LESS THAN (1475280000) ENGINE = InnoDB,
 PARTITION p201610 VALUES LESS THAN (1477958400) ENGINE = InnoDB,
 PARTITION p201611 VALUES LESS THAN (1480550400) ENGINE = InnoDB,
 PARTITION p201612 VALUES LESS THAN (1483228800) ENGINE = InnoDB,
 PARTITION p201701 VALUES LESS THAN (1485907200) ENGINE = InnoDB,
 PARTITION p201702 VALUES LESS THAN (1488326400) ENGINE = InnoDB,
 PARTITION p201703 VALUES LESS THAN (1491004800) ENGINE = InnoDB,
 PARTITION p201704 VALUES LESS THAN (1493596800) ENGINE = InnoDB,
 PARTITION p201705 VALUES LESS THAN (1496275200) ENGINE = InnoDB,
 PARTITION p201706 VALUES LESS THAN (1498867200) ENGINE = InnoDB,
 PARTITION p201707 VALUES LESS THAN (1501545600) ENGINE = InnoDB,
 PARTITION p201708 VALUES LESS THAN (1504224000) ENGINE = InnoDB,
 PARTITION p201709 VALUES LESS THAN (1506816000) ENGINE = InnoDB,
 PARTITION p201710 VALUES LESS THAN (1509494400) ENGINE = InnoDB,
 PARTITION p201711 VALUES LESS THAN (1512086400) ENGINE = InnoDB,
 PARTITION p201712 VALUES LESS THAN (1514764800) ENGINE = InnoDB,
 PARTITION p201801 VALUES LESS THAN (1517443200) ENGINE = InnoDB,
 PARTITION p201802 VALUES LESS THAN (1519862400) ENGINE = InnoDB,
 PARTITION p201803 VALUES LESS THAN (1522540800) ENGINE = InnoDB,
 PARTITION p201804 VALUES LESS THAN (1525132800) ENGINE = InnoDB,
 PARTITION p201805 VALUES LESS THAN (1527811200) ENGINE = InnoDB,
 PARTITION p201806 VALUES LESS THAN (1530403200) ENGINE = InnoDB,
 PARTITION p201807 VALUES LESS THAN (1533081600) ENGINE = InnoDB,
 PARTITION p201808 VALUES LESS THAN (1535760000) ENGINE = InnoDB,
 PARTITION p201809 VALUES LESS THAN (1538352000) ENGINE = InnoDB,
 PARTITION p201810 VALUES LESS THAN (1541030400) ENGINE = InnoDB,
 PARTITION p201811 VALUES LESS THAN (1543622400) ENGINE = InnoDB,
 PARTITION p201812 VALUES LESS THAN (1546300800) ENGINE = InnoDB,
 PARTITION p201901 VALUES LESS THAN (1548979200) ENGINE = InnoDB,
 PARTITION p201902 VALUES LESS THAN (1551398400) ENGINE = InnoDB,
 PARTITION p201903 VALUES LESS THAN (1554076800) ENGINE = InnoDB,
 PARTITION p201904 VALUES LESS THAN (1556668800) ENGINE = InnoDB,
 PARTITION p201905 VALUES LESS THAN (1559347200) ENGINE = InnoDB,
 PARTITION p201906 VALUES LESS THAN (1561939200) ENGINE = InnoDB,
 PARTITION p201907 VALUES LESS THAN (1564617600) ENGINE = InnoDB,
 PARTITION p201908 VALUES LESS THAN (1567296000) ENGINE = InnoDB,
 PARTITION p201909 VALUES LESS THAN (1569888000) ENGINE = InnoDB,
 PARTITION p201910 VALUES LESS THAN (1572566400) ENGINE = InnoDB,
 PARTITION p201911 VALUES LESS THAN (1575158400) ENGINE = InnoDB,
 PARTITION p201912 VALUES LESS THAN (1577836800) ENGINE = InnoDB,
 PARTITION p202001 VALUES LESS THAN (1580515200) ENGINE = InnoDB,
 PARTITION p202002 VALUES LESS THAN (1583020800) ENGINE = InnoDB,
 PARTITION p202003 VALUES LESS THAN (1585699200) ENGINE = InnoDB,
 PARTITION p202004 VALUES LESS THAN (1588291200) ENGINE = InnoDB,
 PARTITION p202005 VALUES LESS THAN (1590969600) ENGINE = InnoDB,
 PARTITION p202006 VALUES LESS THAN (1593561600) ENGINE = InnoDB,
 PARTITION p202007 VALUES LESS THAN (1596240000) ENGINE = InnoDB,
 PARTITION p202008 VALUES LESS THAN (1598918400) ENGINE = InnoDB,
 PARTITION p202009 VALUES LESS THAN (1601510400) ENGINE = InnoDB,
 PARTITION p202010 VALUES LESS THAN (1604188800) ENGINE = InnoDB,
 PARTITION p202011 VALUES LESS THAN (1606780800) ENGINE = InnoDB,
 PARTITION p202012 VALUES LESS THAN (1609459200) ENGINE = InnoDB,
 PARTITION p202101 VALUES LESS THAN (1612137600) ENGINE = InnoDB,
 PARTITION p202102 VALUES LESS THAN (1614556800) ENGINE = InnoDB,
 PARTITION p202103 VALUES LESS THAN (1617235200) ENGINE = InnoDB,
 PARTITION p202104 VALUES LESS THAN (1619827200) ENGINE = InnoDB,
 PARTITION p202105 VALUES LESS THAN (1622505600) ENGINE = InnoDB,
 PARTITION p202106 VALUES LESS THAN (1625097600) ENGINE = InnoDB,
 PARTITION p202107 VALUES LESS THAN (1627776000) ENGINE = InnoDB,
 PARTITION p202108 VALUES LESS THAN (1630454400) ENGINE = InnoDB,
 PARTITION p202109 VALUES LESS THAN (1633046400) ENGINE = InnoDB,
 PARTITION p202110 VALUES LESS THAN (1635724800) ENGINE = InnoDB,
 PARTITION p202111 VALUES LESS THAN (1638316800) ENGINE = InnoDB,
 PARTITION p202112 VALUES LESS THAN (1640995200) ENGINE = InnoDB,
 PARTITION p202201 VALUES LESS THAN (1643673600) ENGINE = InnoDB,
 PARTITION p202202 VALUES LESS THAN (1646092800) ENGINE = InnoDB,
 PARTITION p202203 VALUES LESS THAN (1648771200) ENGINE = InnoDB,
 PARTITION p202204 VALUES LESS THAN (1651363200) ENGINE = InnoDB,
 PARTITION p202205 VALUES LESS THAN (1654041600) ENGINE = InnoDB,
 PARTITION p202206 VALUES LESS THAN (1656633600) ENGINE = InnoDB,
 PARTITION p202207 VALUES LESS THAN (1659312000) ENGINE = InnoDB,
 PARTITION p202208 VALUES LESS THAN (1661990400) ENGINE = InnoDB,
 PARTITION p202209 VALUES LESS THAN (1664582400) ENGINE = InnoDB,
 PARTITION p202210 VALUES LESS THAN (1667260800) ENGINE = InnoDB,
 PARTITION p202211 VALUES LESS THAN (1669852800) ENGINE = InnoDB,
 PARTITION p202212 VALUES LESS THAN (1672531200) ENGINE = InnoDB,
 PARTITION p202301 VALUES LESS THAN (1675209600) ENGINE = InnoDB,
 PARTITION p202302 VALUES LESS THAN (1677628800) ENGINE = InnoDB,
 PARTITION p202303 VALUES LESS THAN (1680307200) ENGINE = InnoDB,
 PARTITION p202304 VALUES LESS THAN (1682899200) ENGINE = InnoDB,
 PARTITION p202305 VALUES LESS THAN (1685577600) ENGINE = InnoDB,
 PARTITION p202306 VALUES LESS THAN (1688169600) ENGINE = InnoDB,
 PARTITION p202307 VALUES LESS THAN (1690848000) ENGINE = InnoDB,
 PARTITION p202308 VALUES LESS THAN (1693526400) ENGINE = InnoDB,
 PARTITION p202309 VALUES LESS THAN (1696118400) ENGINE = InnoDB,
 PARTITION p202310 VALUES LESS THAN (1698796800) ENGINE = InnoDB,
 PARTITION p202311 VALUES LESS THAN (1701388800) ENGINE = InnoDB,
 PARTITION p202312 VALUES LESS THAN (1704067200) ENGINE = InnoDB,
 PARTITION p202401 VALUES LESS THAN (1706745600) ENGINE = InnoDB,
 PARTITION p202402 VALUES LESS THAN (1709251200) ENGINE = InnoDB,
 PARTITION p202403 VALUES LESS THAN (1711929600) ENGINE = InnoDB,
 PARTITION p202404 VALUES LESS THAN (1714521600) ENGINE = InnoDB,
 PARTITION p202405 VALUES LESS THAN (1717200000) ENGINE = InnoDB,
 PARTITION p202406 VALUES LESS THAN (1719792000) ENGINE = InnoDB,
 PARTITION p202407 VALUES LESS THAN (1722470400) ENGINE = InnoDB,
 PARTITION p202408 VALUES LESS THAN (1725148800) ENGINE = InnoDB,
 PARTITION p202409 VALUES LESS THAN (1727740800) ENGINE = InnoDB,
 PARTITION p202410 VALUES LESS THAN (1730419200) ENGINE = InnoDB,
 PARTITION p202411 VALUES LESS THAN (1733011200) ENGINE = InnoDB,
 PARTITION p202412 VALUES LESS THAN (1735689600) ENGINE = InnoDB,
 PARTITION p202501 VALUES LESS THAN (1738368000) ENGINE = InnoDB,
 PARTITION p202502 VALUES LESS THAN (1740787200) ENGINE = InnoDB,
 PARTITION p202503 VALUES LESS THAN (1743465600) ENGINE = InnoDB,
 PARTITION p202504 VALUES LESS THAN (1746057600) ENGINE = InnoDB,
 PARTITION p202505 VALUES LESS THAN (1748736000) ENGINE = InnoDB,
 PARTITION p202506 VALUES LESS THAN (1751328000) ENGINE = InnoDB,
 PARTITION p202507 VALUES LESS THAN (1754006400) ENGINE = InnoDB,
 PARTITION p202508 VALUES LESS THAN (1756684800) ENGINE = InnoDB,
 PARTITION p202509 VALUES LESS THAN (1759276800) ENGINE = InnoDB,
 PARTITION p202510 VALUES LESS THAN (1761955200) ENGINE = InnoDB,
 PARTITION p202511 VALUES LESS THAN (1764547200) ENGINE = InnoDB,
 PARTITION p202512 VALUES LESS THAN (1767225600) ENGINE = InnoDB,
 PARTITION p202601 VALUES LESS THAN (1769904000) ENGINE = InnoDB,
 PARTITION p202602 VALUES LESS THAN (1772323200) ENGINE = InnoDB,
 PARTITION p202603 VALUES LESS THAN (1775001600) ENGINE = InnoDB,
 PARTITION p202604 VALUES LESS THAN (1777593600) ENGINE = InnoDB,
 PARTITION p202605 VALUES LESS THAN (1780272000) ENGINE = InnoDB,
 PARTITION p202606 VALUES LESS THAN (1782864000) ENGINE = InnoDB,
 PARTITION p202607 VALUES LESS THAN (1785542400) ENGINE = InnoDB,
 PARTITION p202608 VALUES LESS THAN (1788220800) ENGINE = InnoDB,
 PARTITION p202609 VALUES LESS THAN (1790812800) ENGINE = InnoDB,
 PARTITION p202610 VALUES LESS THAN (1793491200) ENGINE = InnoDB,
 PARTITION p202611 VALUES LESS THAN (1796083200) ENGINE = InnoDB,
 PARTITION p202612 VALUES LESS THAN (1798761600) ENGINE = InnoDB,
 PARTITION p202701 VALUES LESS THAN (1801440000) ENGINE = InnoDB,
 PARTITION p202702 VALUES LESS THAN (1803859200) ENGINE = InnoDB,
 PARTITION p202703 VALUES LESS THAN (1806537600) ENGINE = InnoDB,
 PARTITION p202704 VALUES LESS THAN (1809129600) ENGINE = InnoDB,
 PARTITION p202705 VALUES LESS THAN (1811808000) ENGINE = InnoDB,
 PARTITION p202706 VALUES LESS THAN (1814400000) ENGINE = InnoDB,
 PARTITION p202707 VALUES LESS THAN (1817078400) ENGINE = InnoDB,
 PARTITION p202708 VALUES LESS THAN (1819756800) ENGINE = InnoDB,
 PARTITION p202709 VALUES LESS THAN (1822348800) ENGINE = InnoDB,
 PARTITION p202710 VALUES LESS THAN (1825027200) ENGINE = InnoDB,
 PARTITION p202711 VALUES LESS THAN (1827619200) ENGINE = InnoDB,
 PARTITION p202712 VALUES LESS THAN (1830297600) ENGINE = InnoDB,
 PARTITION p202801 VALUES LESS THAN (1832976000) ENGINE = InnoDB,
 PARTITION p202802 VALUES LESS THAN (1835481600) ENGINE = InnoDB,
 PARTITION p202803 VALUES LESS THAN (1838160000) ENGINE = InnoDB,
 PARTITION p202804 VALUES LESS THAN (1840752000) ENGINE = InnoDB,
 PARTITION p202805 VALUES LESS THAN (1843430400) ENGINE = InnoDB,
 PARTITION p202806 VALUES LESS THAN (1846022400) ENGINE = InnoDB,
 PARTITION p202807 VALUES LESS THAN (1848700800) ENGINE = InnoDB,
 PARTITION p202808 VALUES LESS THAN (1851379200) ENGINE = InnoDB,
 PARTITION p202809 VALUES LESS THAN (1853971200) ENGINE = InnoDB,
 PARTITION p202810 VALUES LESS THAN (1856649600) ENGINE = InnoDB,
 PARTITION p202811 VALUES LESS THAN (1859241600) ENGINE = InnoDB,
 PARTITION p202812 VALUES LESS THAN (1861920000) ENGINE = InnoDB,
 PARTITION p202901 VALUES LESS THAN (1864598400) ENGINE = InnoDB,
 PARTITION p202902 VALUES LESS THAN (1867017600) ENGINE = InnoDB,
 PARTITION p202903 VALUES LESS THAN (1869696000) ENGINE = InnoDB,
 PARTITION p202904 VALUES LESS THAN (1872288000) ENGINE = InnoDB,
 PARTITION p202905 VALUES LESS THAN (1874966400) ENGINE = InnoDB,
 PARTITION p202906 VALUES LESS THAN (1877558400) ENGINE = InnoDB,
 PARTITION p202907 VALUES LESS THAN (1880236800) ENGINE = InnoDB,
 PARTITION p202908 VALUES LESS THAN (1882915200) ENGINE = InnoDB,
 PARTITION p202909 VALUES LESS THAN (1885507200) ENGINE = InnoDB,
 PARTITION p202910 VALUES LESS THAN (1888185600) ENGINE = InnoDB,
 PARTITION p202911 VALUES LESS THAN (1890777600) ENGINE = InnoDB,
 PARTITION p202912 VALUES LESS THAN (1893456000) ENGINE = InnoDB,
 PARTITION p203001 VALUES LESS THAN (1896134400) ENGINE = InnoDB,
 PARTITION p203002 VALUES LESS THAN (1898553600) ENGINE = InnoDB,
 PARTITION p203003 VALUES LESS THAN (1901232000) ENGINE = InnoDB,
 PARTITION p203004 VALUES LESS THAN (1903824000) ENGINE = InnoDB,
 PARTITION p203005 VALUES LESS THAN (1906502400) ENGINE = InnoDB,
 PARTITION p203006 VALUES LESS THAN (1909094400) ENGINE = InnoDB,
 PARTITION p203007 VALUES LESS THAN (1911772800) ENGINE = InnoDB,
 PARTITION p203008 VALUES LESS THAN (1914451200) ENGINE = InnoDB,
 PARTITION p203009 VALUES LESS THAN (1917043200) ENGINE = InnoDB,
 PARTITION p203010 VALUES LESS THAN (1919721600) ENGINE = InnoDB,
 PARTITION p203011 VALUES LESS THAN (1922313600) ENGINE = InnoDB,
 PARTITION p203012 VALUES LESS THAN (1924992000) ENGINE = InnoDB,
 PARTITION p203101 VALUES LESS THAN (1927670400) ENGINE = InnoDB,
 PARTITION p203102 VALUES LESS THAN (1930089600) ENGINE = InnoDB,
 PARTITION p203103 VALUES LESS THAN (1932768000) ENGINE = InnoDB,
 PARTITION p203104 VALUES LESS THAN (1935360000) ENGINE = InnoDB,
 PARTITION p203105 VALUES LESS THAN (1938038400) ENGINE = InnoDB,
 PARTITION p203106 VALUES LESS THAN (1940630400) ENGINE = InnoDB,
 PARTITION p203107 VALUES LESS THAN (1943308800) ENGINE = InnoDB,
 PARTITION p203108 VALUES LESS THAN (1945987200) ENGINE = InnoDB,
 PARTITION p203109 VALUES LESS THAN (1948579200) ENGINE = InnoDB,
 PARTITION p203110 VALUES LESS THAN (1951257600) ENGINE = InnoDB,
 PARTITION p203111 VALUES LESS THAN (1953849600) ENGINE = InnoDB,
 PARTITION p203112 VALUES LESS THAN (1956528000) ENGINE = InnoDB,
 PARTITION p203201 VALUES LESS THAN (1959206400) ENGINE = InnoDB,
 PARTITION p203202 VALUES LESS THAN (1961712000) ENGINE = InnoDB,
 PARTITION p203203 VALUES LESS THAN (1964390400) ENGINE = InnoDB,
 PARTITION p203204 VALUES LESS THAN (1966982400) ENGINE = InnoDB,
 PARTITION p203205 VALUES LESS THAN (1969660800) ENGINE = InnoDB,
 PARTITION p203206 VALUES LESS THAN (1972252800) ENGINE = InnoDB,
 PARTITION p203207 VALUES LESS THAN (1974931200) ENGINE = InnoDB,
 PARTITION p203208 VALUES LESS THAN (1977609600) ENGINE = InnoDB,
 PARTITION p203209 VALUES LESS THAN (1980201600) ENGINE = InnoDB,
 PARTITION p203210 VALUES LESS THAN (1982880000) ENGINE = InnoDB,
 PARTITION p203211 VALUES LESS THAN (1985472000) ENGINE = InnoDB,
 PARTITION p203212 VALUES LESS THAN (1988150400) ENGINE = InnoDB,
 PARTITION pmax VALUES LESS THAN MAXVALUE ENGINE = InnoDB)
SQL;
        return $query;
    }
}
