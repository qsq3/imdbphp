<?php
 #############################################################################
 # PHP MovieAPI                                          (c) Itzchak Rehberg #
 # written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
 # http://www.izzysoft.de/                                                   #
 # ------------------------------------------------------------------------- #
 # This program is free software; you can redistribute and/or modify it      #
 # under the terms of the GNU General Public License (see doc/LICENSE)       #
 #############################################################################

namespace Imdb;

/**
 * Accessing Movie information
 * @author Georgos Giagas
 * @author Izzy (izzysoft AT qumran DOT org)
 * @copyright (c) 2002-2004 by Giorgos Giagas and (c) 2004-2009 by Itzchak Rehberg and IzzySoft
 */
class MdbBase extends Config {
  public $version = '2.6.1';

  protected $months = array(
      "January" => "01",
      "February" => "02",
      "March" => "03",
      "April" => "04",
      "May" => "05",
      "June" => "06",
      "July" => "07",
      "August" => "08",
      "September" => "09",
      "October" => "10",
      "November" => "11",
      "December" => "12"
    );

  /**
   * @var Cache
   */
  protected $cache;

  /**
   * @var Logger
   */
  protected $logger;

  /**
   * @var Config
   */
  protected $config;

  protected $page = array();


  /**
   * @param Config $config OPTIONAL override default config
   */
  public function __construct(Config $config = null) {
    parent::__construct();

    if ($config) {
      foreach ($config as $key => $value) {
        $this->$key = $value;
      }
    }

    $this->config = $config ?: $this;
    $this->logger = new Logger($this->debug);
    $this->cache = new Cache($this->config, $this->logger);

    if ($this->storecache && ($this->cache_expire > 0)) {
      $this->cache->purge();
    }
  }

  /**
   * Setup class for a new IMDB id
   * @param string id IMDBID of the requested movie
   * @TODO remove this / make it private
   * @TODO allow numeric ids and coerce them into 7 digit strings
   */
  public function setid ($id) {
    if (!preg_match("/^\d{7}$/",$id)) $this->debug_scalar("<BR>setid: Invalid IMDB ID '$id'!<BR>");
    $this->imdbID = $id;
    $this->reset_vars();
  }

  /**
   * Retrieve the IMDB ID
   * @return string id IMDBID currently used
   */
  public function imdbid() {
    return $this->imdbID;
  }

 #---------------------------------------------------------[ Debug helpers ]---
  protected function debug_scalar($scalar) {
    $this->logger->error($scalar);
  }
  protected function debug_object($object) {
    $this->logger->error('{object}', array('object' => $object));
  }
  protected function debug_html($html) {
    $this->logger->error(htmlentities($html));
  }

  /**
   * Get numerical value for month name
   * @param string name name of month
   * @return integer month number
   */
  protected function monthNo($mon) {
    return @$this->months[$mon];
  }

 #-------------------------------------------------------------[ Open Page ]---
  /**
   * Get a page from IMDb, which will be cached in memory for repeated use
   * @param string $page Name of the page to retrieve e.g. Title, Credits
   * @return string
   * @see mdb_base->set_pagename()
   */
  protected function getPage($page) {
    if (!empty($this->page[$page])) {
      return $this->page[$page];
    }

    $pageRequest = new Page($this->buildUrl($page), $this->config, $this->cache, $this->logger);

    $this->page[$page] = $pageRequest->get();

    return $this->page[$page];
  }

  /**
   * Overrideable method to build the URL used by getPage
   * @param string $page
   * @return string
   */
  protected function buildUrl($page = null) {
    return '';
  }

  /**
   * Reset page vars
   */
  protected function reset_vars() {
    return;
  }

}