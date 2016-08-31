<?php

class BaseModel{

	protected $table;
	protected $fillable = [];
	protected $primaryKey = 'id';
	protected $db;
    private $attrs = array();
	public $exists = false;

	public function __construct(array $attributes = [])
    {
    	$this->db = new Db($GLOBALS['database']);
        $this->fill($attributes);
    }

    public function __set($name, $val){
        $this->attrs[$name] = $val;
    }

    public function __get($name){
        if(array_key_exists($name, $this->attrs))
            return $this->attrs[$name];
        return false;
    }

    public function fill(array $attributes)
    {
    	foreach($attributes as $key=>$value)
    		if($this->isFillable($key))
    			$this->$key=$value;
    	return $this;
    }

    public function isFillable($key)
    {
    	if (in_array($key, $this->fillable))
            return true;
        return false;
    }

 	public static function create(array $attributes = [])
 	{
 		$model = new static($attributes);
        $model->save();
        return $model;
    }

    public function save()
    {
    	if ($this->exists) {
            foreach($this->attrs as $key => $val){
            	if(in_array($key, $this->fillable) && $key != $this->primaryKey)
            		$att[$key] = $this->$key;
            }
            $this->update($att);
        }
        else
        {
        	foreach($this->attrs as $key => $val){
            	if(in_array($key, $this->fillable))
            		$att[$key] = $this->$key;
            }
            if($this->primaryKey == "id")
                $this->id = $this->db->create($this->getTable(), $att);
            else{
                $this->db->create($this->getTable(), $att);
                $primaryKey = $this->primaryKey;
                $primKeyInserted = $att[$primaryKey];
                $this->$primaryKey = $primKeyInserted;
        	}
            $this->exists = true;
        }
        return $this;
    }

    public function update(array $attributes)
    {
    	$primaryKey = $this->primaryKey;
        unset($attributes[$primaryKey]);
    	$this->fill($attributes);
    	$this->db->update($this->getTable(), $attributes, [$this->primaryKey => $this->$primaryKey]);
    	return $this;
    }

    public function getTable(){
    	if(empty($this->table))
    		return get_class($this) . 's';
    	return $this->table;
    }

    public function destroy(){
    	$this->exists = false;
    	$primaryKey = $this->primaryKey;
    	$this->db->destroy($this->getTable(), [$this->primaryKey => $this->$primaryKey]);
    }

    public function fillFromTable($attributes = []){
		foreach($attributes as $key=>$val){
			$this->$key = $val;
			$this->exists = true;
		}
		return $this;
    }

    public static function find($prim){
    	$model = new static();
       	$tableName = $model->getTable();
       	$attributes = Db::find($tableName, $model->primaryKey, $prim);
    	if(!$attributes)
    		return false;
		$model->fillFromTable($attributes);
		return $model;
    }

    public static function findOrFail($prim){
        $model = new static();
        $model = $model->find($prim);
        if($model == false)
            die(getError(11));
        return $model;
    }

    public static function where($column, $value){
    	$model = new static();
    	$tableName = $model->getTable();
    	$allAttributes = Db::where($tableName, $column, $value);
    	if(!$allAttributes)
    		return false;
    	foreach($allAttributes as $values){
    		$newModel = new static();
    		$newModel->fillFromTable($values);
    		$modelsArray[] = $newModel;
    	}
    	return $modelsArray;
    }

}