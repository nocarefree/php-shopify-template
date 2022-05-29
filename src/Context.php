<?php

namespace Ncf\ShopifyLiquid;

use Liquid\LiquidException;
use Illuminate\Support\Arr;

class Context extends \Liquid\Context{


    public function __construct(array $assigns = array(), array $registers = array()){
        parent::__construct($assigns, $registers);
		$this->app = $this->registers['_app']??null;
    }

	public function push(){
		if(count($this->assigns) > 15){
			throw new LiquidException("Nesting too deep");
		}

		$this->assigns[] = $this->app instanceof ShopifyTemplate ? $this->registers['_app']->getAssigns() : [];
	
	}
	
	public function get($key) {
		return $this->resolve($key);
	}

	public function set($key, $value, $global = false) {
		if($global){
			foreach($this->assigns as &$assigns){
				Arr::set($assigns, $key, $value);
			}
		}else{
			$assigns = &end($this->assigns);
			Arr::set($assigns, $key, $value);
		}
		
		return $this;
	}

    private function resolve($key) {
		// This shouldn't happen
		if (is_array($key)) {
			throw new LiquidException("Cannot resolve arrays as key");
		}

		if (is_null($key) || $key == 'null') {
			return null;
		}

		if ($key == 'true') {
			return true;
		}

		if ($key == 'false') {
			return false;
		}

		if (preg_match('/^\'(.*)\'$/', $key, $matches)) {
			return $matches[1];
		}

		if (preg_match('/^"(.*)"$/', $key, $matches)) {
			return $matches[1];
		}

		if (preg_match('/^(\d+)$/', $key, $matches)) {
			return $matches[1];
		}

		if (preg_match('/^(\d[\d\.]+)$/', $key, $matches)) {
			return $matches[1];
		}

		return $this->variable($key);
	}


	private function variable($key) {
		// TagDecrement depends on environments being checked before assigns
		foreach ($this->environments as $environment) {
			if (array_key_exists($key, $environment)) {
				return $environment[$key];
			}
		}

		return Arr::get($this->lastAssigns(), $key, null);
	}

	private function lastAssigns(){
		return end($this->assigns);
	}
    
}