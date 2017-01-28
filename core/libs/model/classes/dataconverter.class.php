<?php 
class myDataConverter extends myRegistry {
	public function convertToSave($data, $metadata) {
		if(array_key_exists('fields', $metadata)) {
			foreach($data as $key => $value) {
				if(array_key_exists($key, $metadata['fields'])) {
					$data[$key] = $this->convertFieldToSave($value, $metadata['fields'][$key]);
				}
			}
		}

		return $data;
	}

	public function convertFromSave($data, $metadata) {
		if(array_key_exists('fields', $metadata)) {
			foreach($data as $key => $value) {
				if(array_key_exists($key, $metadata['fields'])) {
					$data[$key] = $this->convertFieldFromSave($value, $metadata['fields'][$key]);
				}
			}
		}

		return $data;
	}

	public function convertFieldToSave($value, $metadata) {
		$from = isset($metadata['phptype']) ? $metadata['phptype'] : 'x';
		$to = isset($metadata['dbtype']) ? $metadata['dbtype'] : 'x';
		$method_name = 'convertFrom_'.$from.'_To_'.$to;

		if(method_exists($this, $method_name)) {
			$value = call_user_func(array($this, $method_name), $value);
		}

		return $value;
	}

	public function convertFieldFromSave($value, $metadata) {
		$to = isset($metadata['phptype']) ? $metadata['phptype'] : 'x';
		$from = isset($metadata['dbtype']) ? $metadata['dbtype'] : 'x';
		$method_name = 'convertFrom_'.$from.'_To_'.$to;

		$method_names = array (
			'convertFrom_'.$from.'_To_'.$to,
			'convertFrom_x_To_'.$to,
			'convertFrom_x_To_x',
		);

		foreach($method_names as $method_name) {
			if(method_exists($this, $method_name)) {
				$value = call_user_func(array($this, $method_name), $value);
				break;
			}
		}

		return $value;
	}

	public function convertFrom_x_To_x($value) {
		return $value;
	}

	public function convertFrom_x_To_boolean($value) {
		return $this->convertFrom_x_To_bool($value);
	}

	public function convertFrom_x_To_bool($value) {
		return $value ? true : false;
	}
}

return 'myDataConverter';