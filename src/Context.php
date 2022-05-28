<?php

namespace Ncf\ShopifyLiquid;

use Liquid\LiquidException;
use Illuminate\Support\Arr;

class Context extends \Liquid\Context{


    public function __construct(array $assigns = array(), array $registers = array()){
        parent::__construct($assigns, $registers);
    }

	/**
	 * Replaces []
	 *
	 * @param string
	 *
	 * @return mixed
	 */
	public function get($key) {
		return $this->resolve($key);
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

		return Arr::get($this->assigns[0], $key, null);
	}
    
}