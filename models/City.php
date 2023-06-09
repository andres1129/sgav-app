<?php
    namespace Models;
    class City{
        protected static $conn;
        protected static $columnsTbl=['id_city','name_city','id_region'];
        private $id_city;
        private $name_city;
        private $id_region;
        public function __construct($args = []){
            $this->id_city = $args['id_city'] ?? '';
            $this->name_city = $args['name_city'] ?? '';
            $this->id_region = $args['id_region'] ?? '';
            
        }
        public function saveData($data){
            $delimiter = ":";
            $dataBd = $this->sanitizarAttributos();
            $valCols = $delimiter . join(',:',array_keys($data));
            $cols = join(',',array_keys($data));
            $sql = "INSERT INTO cities ($cols) VALUES ($valCols)";
            $stmt= self::$conn->prepare($sql);
            $stmt->execute($data);
        }
        public function loadAllData(){
            $sql = "SELECT id_city,name_city,id_region FROM cities";
            $stmt= self::$conn->prepare($sql);
            $stmt->execute();
            $paises = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $paises;
        }
        public function loadByIdData($id){
            $sql = "SELECT id_city,name_city,id_region FROM cities WHERE id_city = :id_city";
            $stmt= self::$conn->prepare($sql);
            $stmt->bindParam(':id_city', $id, \PDO::PARAM_INT); 
            $stmt->execute();
            $pais = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $pais;
        }
        public function deleteByIdData($id){
            $response=[];
            $sql = "DELETE FROM cities WHERE id_city = :id_city";
            $stmt= self::$conn->prepare($sql);
            $stmt->bindParam(':id_city', $id, \PDO::PARAM_INT); 
            $stmt->execute();
            if ($stmt->rowCount()>0){
                $response=[[
                    'mensaje' => 'El registro fue eliminado correctamente',
                    'codEstado' => '200',
                    'totalreg' => $stmt->rowCount()
                ]];
            }else{
                $response=[[
                    'mensaje' => 'El registro no fue eliminado',
                    'reject' => 'Registro no encontrado o no existe',
                    'codEstado' => '204',
                    'totalreg' => $stmt->rowCount()
                ]];
            }
            return $response;
        }
        public static function setConn($connBd){
            self::$conn = $connBd;
        }
        public function atributos(){
            $atributos = [];
            foreach (self::$columnsTbl as $columna){
                if($columna === 'id_city') continue;
                $atributos [$columna]=$this->$columna;
             }
             return $atributos;
        }
        public function sanitizarAttributos(){
            $atributos = $this->atributos();
            $sanitizado = [];
            foreach($atributos as $key => $value){
                $sanitizado[$key] = self::$conn->quote($value);
            }
            return $sanitizado;
        }
    }
?>