<?php

class Transporte_model extends CI_Model {
    //constructor de la clase
    function __construct() {
        //LLamar al constructor del Modelo
        parent::__construct();
    }
	function consultar_seccion_usuario($nr=0)
	{
					
		$sentencia="
			SELECT eil.id_empleado_informacion_laboral, 
					eil.id_empleado, 
					eil.id_seccion, 
					eil.fecha_inicio 
			FROM sir_empleado e JOIN sir_empleado_informacion_laboral eil ON eil.id_empleado = e.id_empleado 
								JOIN tcm_empleado_informacion_laboral veil ON veil.id_empleado = eil.id_empleado 
								AND veil.fecha_inicio = eil.fecha_inicio AND e.nr = '".$nr."'
		";
		//echo $sentencia;
		$query=$this->db->query($sentencia);
		
		if($query->num_rows() > 0) {
			return $query->row_array();
		}
		else {
			return array(
				'id_nivel_1' => 0
			);
		}
	}
	
	////////////////////////////////FUNCION QUE CARGA lOS USUARIOS DE SITCOM//////////////////////
	
	function usuario_sitcom()
	{
		$query=$this->db->query("
			SELECT u.id_usuario, LOWER(u.nombre_completo) AS usuario
			FROM org_usuario u
			INNER JOIN org_usuario_rol AS ur ON (ur.id_usuario=u.id_usuario)
			INNER JOIN org_rol_modulo_permiso AS rmp ON (rmp.id_rol=ur.id_rol)
			INNER JOIN org_modulo AS m ON (m.id_modulo=rmp.id_modulo)
			WHERE m.id_sistema=5
			GROUP BY u.nombre_completo;
		");
		return $query->result();		
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////	
	
	function consultar_empleados($nr=0, $id_solicitud=NULL) 
	{
		$where=" ";
		if($id_solicitud!=NULL){
			$where=" AND sir_empleado.id_empleado NOT IN (
			SELECT a.id_empleado FROM tcm_acompanante a WHERE a.id_solicitud_transporte = ".$id_solicitud.")";

		}

		$sentencia="SELECT
					sir_empleado.id_empleado AS NR,
					LOWER(CONCAT_WS(' ',sir_empleado.primer_nombre, sir_empleado.segundo_nombre, sir_empleado.tercer_nombre, sir_empleado.primer_apellido, sir_empleado.segundo_apellido, sir_empleado.apellido_casada)) AS nombre
					FROM sir_empleado
					WHERE sir_empleado.id_estado=1 and sir_empleado.NR<>'".$nr."'".$where;
		$query=$this->db->query($sentencia);
		if($query->num_rows() > 0) {
			return $query->result_array();
		}
		else {
			return 0;
		}
	}
	
	function consultar_empleado($nr=0) 
	{
		$sentencia="SELECT
					sir_empleado.id_empleado AS NR,
					LOWER(CONCAT_WS(' ',sir_empleado.primer_nombre, sir_empleado.segundo_nombre, sir_empleado.tercer_nombre, sir_empleado.primer_apellido, sir_empleado.segundo_apellido, sir_empleado.apellido_casada)) AS nombre,
					correo
					FROM sir_empleado
					WHERE sir_empleado.NR='".$nr."'";
		$query=$this->db->query($sentencia);
		if($query->num_rows() > 0) {
			return $query->result_array();		}
		else {
			return 0;
		}
	}
	
	function consultar_empl($nr) 
	{
		$sentencia="SELECT id_empleado FROM tcm_empleado where nr like '$nr'";
		$query=$this->db->query($sentencia);
		if($query->num_rows() > 0) {
			return $query->result_array();
		}
		else {
			return 0;
		}
	}
	
	function consultar_cargo($id)
	{
		$query=$this->db->query("
			select f.funcional, n.cargo_nominal
			from sir_empleado as e
			inner join sir_empleado_informacion_laboral as i on i.id_empleado=e.id_empleado
			inner join sir_cargo_nominal as n on i.id_cargo_nominal=n.id_cargo_nominal
			inner join sir_cargo_funcional as f on i.id_cargo_funcional=f.id_cargo_funcional
			where e.id_empleado='$id'
			");
			if($query->num_rows() > 0) {
				return $query->result_array();
			}
			else {
				return 0;
			}
	}
	
	function consultar_departamentos() 
	{
		$sentencia="SELECT
					org_departamento.id_departamento,
					org_departamento.departamento
					FROM
					org_departamento
					LIMIT 0, 14";
		$query=$this->db->query($sentencia);
		if($query->num_rows() > 0) {
			return $query->result_array();
		}
		else {
			return 0;
		}
	}

	function consultar_municipios() 
	{
		$sentencia="SELECT
					org_municipio.id_municipio AS id,
					LOWER(CONCAT_WS(', ', org_municipio.municipio, org_departamento.departamento)) AS nombre
					FROM
					org_municipio
					INNER JOIN org_departamento ON org_municipio.id_departamento_pais = org_departamento.id_departamento
					ORDER BY org_departamento.departamento, org_municipio.municipio";
		$query=$this->db->query($sentencia);
		if($query->num_rows() > 0) {
			return $query->result_array();
		}
		else {
			return 0;
		}
	}
	
	function solicitudes_por_seccion_estado($seccion, $estado,$id){

	  $query=$this->db->query("
		SELECT DISTINCT
		id_solicitud_transporte id,
		DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
		DATE_FORMAT(hora_entrada,'%h:%i %p') entrada,
		DATE_FORMAT(hora_salida,'%h:%i %p') salida,
		LOWER(CONCAT_WS(' ',e.primer_nombre, e.segundo_nombre, e.tercer_nombre, e.primer_apellido, e.segundo_apellido, e.apellido_casada)) AS nombre,
		st.estado_solicitud_transporte estado,
		s.nombre_seccion seccion, i.id_empleado
		FROM
		sir_empleado_informacion_laboral AS i
		LEFT JOIN org_seccion AS s ON s.id_seccion = i.id_seccion
		LEFT JOIN sir_empleado AS e ON e.id_empleado = i.id_empleado
		INNER JOIN tcm_solicitud_transporte AS st ON st.id_empleado_solicitante = e.id_empleado
		WHERE 
		(i.id_seccion='$seccion' || i.id_seccion in (SELECT distinct(o.id_seccion) FROM org_seccion as o
		inner join tcm_empleado as e on e.id_seccion=o.depende
		where e.id_seccion='$seccion')) and
		e.id_empleado<>'$id' and st.estado_solicitud_transporte = '".$estado."' AND (i.id_empleado, i.fecha_inicio) IN  
		( SELECT id_empleado ,MAX(fecha_inicio)  FROM sir_empleado_informacion_laboral GROUP BY id_empleado  ) 
			");
 
 
   	return $query->result();
		
	}
function todas_solicitudes_por_estado($estado,$id)
{
	  $query=$this->db->query("
		SELECT * FROM
		(
		SELECT id_solicitud_transporte id,
		DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
		DATE_FORMAT(hora_entrada,'%r') entrada,
		DATE_FORMAT(hora_salida,'%r') salida,
		requiere_motorista,
		LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
		LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido, s.apellido_casada)) AS nombre
		FROM tcm_solicitud_transporte  t
		LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
		LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
		LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)
		WHERE
			s.id_empleado<>'$id' and
			(estado_solicitud_transporte='".$estado."')
			and (t.id_empleado_solicitante not in
					(select id_empleado from sir_empleado_informacion_laboral))
				
		UNION
				
		SELECT id_solicitud_transporte id,
		DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
		DATE_FORMAT(hora_entrada,'%r') entrada,
		DATE_FORMAT(hora_salida,'%r') salida,
		requiere_motorista,
		LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
		LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido,s.apellido_casada)) AS nombre
		FROM tcm_solicitud_transporte  t
		LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
		LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
		LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)
		WHERE
			s.id_empleado<>'$id' and
			(estado_solicitud_transporte='".$estado."')
			and 
			(	i.id_empleado_informacion_laboral in
				(
					SELECT se.id_empleado_informacion_laboral
					FROM sir_empleado_informacion_laboral AS se
					INNER JOIN
					(
						SELECT id_empleado, MAX(fecha_inicio) AS fecha FROM sir_empleado_informacion_laboral
						GROUP BY id_empleado
						HAVING COUNT(id_empleado>=1)
					)
					AS ids
					ON (se.id_empleado=ids.id_empleado AND se.fecha_inicio=ids.fecha)
					ORDER BY se.id_empleado_informacion_laboral
				)
			)
		)
		as k
		GROUP BY k.id 
		ORDER BY k.fecha DESC
	");
 
 
   	return $query->result();
		
}

function todas_solicitudes_sanSalvador($estado,$id)
{
			  $query=$this->db->query("
			  	SELECT * FROM
(
SELECT id_solicitud_transporte id,
DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
DATE_FORMAT(hora_entrada,'%r') entrada,
DATE_FORMAT(hora_salida,'%r') salida,
requiere_motorista,
estado_solicitud_transporte estado,
LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido, s.apellido_casada)) AS nombre
FROM tcm_solicitud_transporte  t
LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)
WHERE
	s.id_empleado<>'$id' and
	(estado_solicitud_transporte='".$estado."')
	and (t.id_empleado_solicitante not in
			(select id_empleado from sir_empleado_informacion_laboral))
	and o.id_seccion NOT IN 
			(SELECT id_seccion FROM org_seccion WHERE id_seccion BETWEEN 52 AND 66)
UNION
		
SELECT id_solicitud_transporte id,
DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
DATE_FORMAT(hora_entrada,'%r') entrada,
DATE_FORMAT(hora_salida,'%r') salida,
requiere_motorista,
estado_solicitud_transporte estado,
LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido,s.apellido_casada)) AS nombre
FROM tcm_solicitud_transporte  t
LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)
WHERE 
s.id_empleado<>'$id' and
 o.id_seccion NOT IN 
			(SELECT id_seccion FROM org_seccion WHERE id_seccion BETWEEN 52 AND 66)
	and
	(estado_solicitud_transporte='".$estado."')
	and 
	(	i.id_empleado_informacion_laboral in
		(
			SELECT se.id_empleado_informacion_laboral
			FROM sir_empleado_informacion_laboral AS se
			INNER JOIN
			(
				SELECT id_empleado, MAX(fecha_inicio) AS fecha FROM sir_empleado_informacion_laboral
				GROUP BY id_empleado
				HAVING COUNT(id_empleado>=1)
			)
			AS ids
			ON (se.id_empleado=ids.id_empleado AND se.fecha_inicio=ids.fecha)
			ORDER BY se.id_empleado_informacion_laboral
		)
	)
)
as k
GROUP BY k.id 
ORDER BY k.fecha DESC

			  	");
	   	return $query->result();
}




	/////FUNCION QUE RETORNA lAS SOlICITUDES QUE AÚN NO TIENEN UN VEHICUlO O MOTORISTA ASIGNADO NIVEL LOCAL//////////////////////////////
	function solicitudes_por_asignar($seccion){
	  $query=$this->db->query("
		SELECT * FROM
		(
			SELECT id_solicitud_transporte id,
			DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
			DATE_FORMAT(hora_entrada,'%r') entrada,
			DATE_FORMAT(hora_salida,'%r') salida,
			requiere_motorista,
			LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
			LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido, s.apellido_casada)) AS nombre
			FROM tcm_solicitud_transporte  t
			LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
			LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
			LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)
			WHERE 
				(estado_solicitud_transporte=2 and o.id_seccion='$seccion')
				and (t.id_empleado_solicitante not in
						(select id_empleado from sir_empleado_informacion_laboral))
					
			UNION
					
			SELECT id_solicitud_transporte id,
			DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
			DATE_FORMAT(hora_entrada,'%r') entrada,
			DATE_FORMAT(hora_salida,'%r') salida,
			requiere_motorista,
			LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
			LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido,s.apellido_casada)) AS nombre
			FROM tcm_solicitud_transporte  t
			LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
			LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
			LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)
			WHERE 
				(estado_solicitud_transporte=2 and o.id_seccion='$seccion')
				and 
				(	i.id_empleado_informacion_laboral in
					(
						SELECT se.id_empleado_informacion_laboral
						FROM sir_empleado_informacion_laboral AS se
						INNER JOIN
						(
							SELECT id_empleado, MAX(fecha_inicio) AS fecha FROM sir_empleado_informacion_laboral
							GROUP BY id_empleado
							HAVING COUNT(id_empleado>=1)
						)
						AS ids
						ON (se.id_empleado=ids.id_empleado AND se.fecha_inicio=ids.fecha)
						ORDER BY se.id_empleado_informacion_laboral
					)
				)
		)
		as k
		GROUP BY k.id
		");

	   	return $query->result();
			
	}
	
	/////FUNCION QUE RETORNA lAS SOlICITUDES QUE AÚN NO TIENEN UN VEHICUlO O MOTORISTA ASIGNADO NIVEL ADMINISTRADOR//////////////////////////////
function todas_solicitudes_por_asignar(){
	  $query=$this->db->query("
		SELECT * FROM
		(
			SELECT id_solicitud_transporte id,
			DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
			fecha_mision,
			DATE_FORMAT(hora_entrada,'%r') entrada,
			DATE_FORMAT(hora_salida,'%r') salida,
			requiere_motorista,
			LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
			LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido, s.apellido_casada)) AS nombre
			FROM tcm_solicitud_transporte  t
			LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
			LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
			LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)
			WHERE 
				(estado_solicitud_transporte=2)
				and (t.id_empleado_solicitante not in
						(select id_empleado from sir_empleado_informacion_laboral))
					
			UNION
					
			SELECT id_solicitud_transporte id,
			DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
			fecha_mision,
			DATE_FORMAT(hora_entrada,'%r') entrada,
			DATE_FORMAT(hora_salida,'%r') salida,
			requiere_motorista,
			LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
			LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido,s.apellido_casada)) AS nombre
			FROM tcm_solicitud_transporte  t
			LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
			LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
			LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)
			WHERE 
				(estado_solicitud_transporte=2)
				and 
				(	i.id_empleado_informacion_laboral in
					(
						SELECT se.id_empleado_informacion_laboral
						FROM sir_empleado_informacion_laboral AS se
						INNER JOIN
						(
							SELECT id_empleado, MAX(fecha_inicio) AS fecha FROM sir_empleado_informacion_laboral
							GROUP BY id_empleado
							HAVING COUNT(id_empleado>=1)
						)
						AS ids
						ON (se.id_empleado=ids.id_empleado AND se.fecha_inicio=ids.fecha)
						ORDER BY se.id_empleado_informacion_laboral
					)
				)
		)
		as k
		GROUP BY k.id
		ORDER BY k.fecha_mision DESC
		LIMIT 100
		");

	   	return $query->result();
			
	}
	////////////////////////////////////////////////////////////////////////////////////////
	
	/////FUNCION QUE RETORNA lAS SOlICITUDES QUE AÚN NO TIENEN UN VEHICUlO O MOTORISTA ASIGNADO NIVEL DEPARTAMENTAL/////////////////////////////
function solicitudes_por_asignar_depto(){
	  $query=$this->db->query("
		SELECT * FROM
		(
			SELECT id_solicitud_transporte id,
			DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
			DATE_FORMAT(hora_entrada,'%r') entrada,
			DATE_FORMAT(hora_salida,'%r') salida,
			requiere_motorista,
			LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
			LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido, s.apellido_casada)) AS nombre
			FROM tcm_solicitud_transporte  t
			LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
			LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
			LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)
			WHERE 
				(estado_solicitud_transporte=2 and (o.id_seccion!=52 and o.id_seccion!=53 and o.id_seccion!=54 and o.id_seccion!=55 and o.id_seccion!=56 and o.id_seccion!=57 and o.id_seccion!=58 and o.id_seccion!=59 and o.id_seccion!=60 and o.id_seccion!=61 and o.id_seccion!=64 and o.id_seccion!=65 and o.id_seccion!=66))
				and (t.id_empleado_solicitante not in
						(select id_empleado from sir_empleado_informacion_laboral))
					
			UNION
					
			SELECT id_solicitud_transporte id,
			DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
			DATE_FORMAT(hora_entrada,'%r') entrada,
			DATE_FORMAT(hora_salida,'%r') salida,
			requiere_motorista,
			LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
			LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido,s.apellido_casada)) AS nombre
			FROM tcm_solicitud_transporte  t
			LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
			LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
			LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)
			WHERE 
				(estado_solicitud_transporte=2 and (o.id_seccion!=52 and o.id_seccion!=53 and o.id_seccion!=54 and o.id_seccion!=55 and o.id_seccion!=56 and o.id_seccion!=57 and o.id_seccion!=58 and o.id_seccion!=59 and o.id_seccion!=60 and o.id_seccion!=61 and o.id_seccion!=64 and o.id_seccion!=65 and o.id_seccion!=66))
				and 
				(	i.id_empleado_informacion_laboral in
					(
						SELECT se.id_empleado_informacion_laboral
						FROM sir_empleado_informacion_laboral AS se
						INNER JOIN
						(
							SELECT id_empleado, MAX(fecha_inicio) AS fecha FROM sir_empleado_informacion_laboral
							GROUP BY id_empleado
							HAVING COUNT(id_empleado>=1)
						)
						AS ids
						ON (se.id_empleado=ids.id_empleado AND se.fecha_inicio=ids.fecha)
						ORDER BY se.id_empleado_informacion_laboral
					)
				)
		)
		as k
		GROUP BY k.id
		");

	   	return $query->result();
			
	}
	////////////////////////////////////////////////////////////////////////////////////////
	
/////////FUNCION QUE RETORNA FECHA Y HORARIOS DE UNA SOlICITUD EN ESPECÍFICO/////
	function consultar_fecha_solicitud($id)
	{
		$query=$this->db->query("
		SELECT st.fecha_mision AS fecha, st.hora_salida AS salida, st.hora_entrada AS entrada
		FROM tcm_solicitud_transporte AS st
		WHERE st.id_solicitud_transporte =  '$id';
		");
		return $query->result();
	}
	///////////////////////////////////////////////////////////////////////////////////////

	function vehiculos_disponibles2($fecha,$hentrada,$hsalida,$seccion)
	{
		$query=$this->db->query("
			select v.id_vehiculo, v.placa
			from tcm_vehiculo as v
			where v.id_vehiculo not in
			(
				select avm.id_vehiculo
				from tcm_solicitud_transporte as st
				inner join tcm_asignacion_sol_veh_mot as avm
				on (avm.id_solicitud_transporte=st.id_solicitud_transporte)
				where st.fecha_mision='$fecha' and 
					(
						(st.hora_salida>='$hsalida' and st.hora_salida<='$hentrada')
						 or (st.hora_entrada>='$hsalida' and st.hora_entrada<='$hentrada')
						 or (st.hora_salida<='$hsalida' and st.hora_entrada>='$hentrada')
					 )
				and st.estado_solicitud_transporte=3
			)
			and (id_seccion='$seccion') and v.estado=1
			order by v.id_vehiculo asc;");
				return $query->result();
	}
	
	//////VEHICUlOS DISPONIBlES INClUYE lOS QUE ESTÁN EN MISIONES lOCAlES VERSIÓN OFICINAS ///////////
	function vehiculos_disponibles($fecha,$hentrada,$hsalida,$seccion)
	{

		$query=$this->db->query("
			select v.id_vehiculo, v.placa, vm.nombre, vmo.modelo, vc.nombre_clase, vcon.condicion
			from tcm_vehiculo as v
			inner join tcm_vehiculo_marca as vm on (v.id_marca=vm.id_vehiculo_marca)
			inner join tcm_vehiculo_modelo as vmo on (v.id_modelo=vmo.id_vehiculo_modelo)
			inner join tcm_vehiculo_clase as vc on (v.id_clase=vc.id_vehiculo_clase)
			inner join tcm_vehiculo_condicion as vcon on (v.id_condicion=vcon.id_vehiculo_condicion)
			where v.id_vehiculo not in
			(
				select avm.id_vehiculo
				from tcm_solicitud_transporte as st
				inner join tcm_destino_mision as dm
				on (dm.id_solicitud_transporte=st.id_solicitud_transporte)
				inner join tcm_asignacion_sol_veh_mot as avm
				on (avm.id_solicitud_transporte=st.id_solicitud_transporte)
				where st.fecha_mision='$fecha' and 
					(
						(st.hora_salida>='$hsalida' and st.hora_salida<='$hentrada')
						 or (st.hora_entrada>='$hsalida' and st.hora_entrada<='$hentrada')
						 or (st.hora_salida<='$hsalida' and st.hora_entrada>='$hentrada')
					 )
				and st.estado_solicitud_transporte=3
			)
			and (id_seccion='$seccion') and v.estado=1
			order by v.id_vehiculo asc;");
				return $query->result();
	}
	/////////////////////////////////////////////////////////////////////////////////////
	
	//////VEHICUlOS DISPONIBlES INClUYE lOS QUE ESTÁN EN MISIONES lOCAlES VERSION CENTRAL ///////////
	function vehiculos_disponibles_central($fecha,$hentrada,$hsalida)
	{
		$query=$this->db->query("
			select v.id_vehiculo, v.placa, vm.nombre, vmo.modelo, vc.nombre_clase, vcon.condicion
			from tcm_vehiculo as v
			inner join tcm_vehiculo_marca as vm on (v.id_marca=vm.id_vehiculo_marca)
			inner join tcm_vehiculo_modelo as vmo on (v.id_modelo=vmo.id_vehiculo_modelo)
			inner join tcm_vehiculo_clase as vc on (v.id_clase=vc.id_vehiculo_clase)
			inner join tcm_vehiculo_condicion as vcon on (v.id_condicion=vcon.id_vehiculo_condicion)
			where v.id_vehiculo not in
			(
				select avm.id_vehiculo
				from tcm_solicitud_transporte as st
				inner join tcm_destino_mision as dm
				on (dm.id_solicitud_transporte=st.id_solicitud_transporte)
				inner join tcm_asignacion_sol_veh_mot as avm
				on (avm.id_solicitud_transporte=st.id_solicitud_transporte)
				where st.fecha_mision='$fecha' and 
					(
						(st.hora_salida>='$hsalida' and st.hora_salida<='$hentrada')
						 or (st.hora_entrada>='$hsalida' and st.hora_entrada<='$hentrada')
						 or (st.hora_salida<='$hsalida' and st.hora_entrada>='$hentrada')
					 )
				and st.estado_solicitud_transporte=3
				and dm.id_municipio<>97
			)
			and v.id_seccion NOT IN(52,53,54,55,56,57,58,59,60,61,64,65,66)
			and v.estado=1
			order by v.id_vehiculo asc;");
				return $query->result();
	}
	/////////////////////////////////////////////////////////////////////////////////////
	
	//////VEHICUlOS DISPONIBlES INClUYE lOS QUE ESTÁN EN MISIONES lOCAlES VERSION ADMIN ///////////
	function vehiculos_disponibles_nacional($fecha,$hentrada,$hsalida)
	{
		$query=$this->db->query("
			select v.id_vehiculo, v.placa, vm.nombre, vmo.modelo, vc.nombre_clase, vcon.condicion
			from tcm_vehiculo as v
			inner join tcm_vehiculo_marca as vm on (v.id_marca=vm.id_vehiculo_marca)
			inner join tcm_vehiculo_modelo as vmo on (v.id_modelo=vmo.id_vehiculo_modelo)
			inner join tcm_vehiculo_clase as vc on (v.id_clase=vc.id_vehiculo_clase)
			inner join tcm_vehiculo_condicion as vcon on (v.id_condicion=vcon.id_vehiculo_condicion)
			where v.id_vehiculo not in
			(
				select avm.id_vehiculo
				from tcm_solicitud_transporte as st
				inner join tcm_destino_mision as dm
				on (dm.id_solicitud_transporte=st.id_solicitud_transporte)
				inner join tcm_asignacion_sol_veh_mot as avm
				on (avm.id_solicitud_transporte=st.id_solicitud_transporte)
				where st.fecha_mision='$fecha' and 
					(
						(st.hora_salida>='$hsalida' and st.hora_salida<='$hentrada')
						 or (st.hora_entrada>='$hsalida' and st.hora_entrada<='$hentrada')
						 or (st.hora_salida<='$hsalida' and st.hora_entrada>='$hentrada')
					 )
				and st.estado_solicitud_transporte=3
				and dm.id_municipio<>97
			)
			and v.estado=1
			order by v.id_vehiculo asc;");
				return $query->result();
	}
	/////////////////////////////////////////////////////////////////////////////////////
	
	//////////////////////VEHICUlOS EN MISION lOCAl/////////////////////////////////
	
	function vehiculo_en_mision_local($fecha,$hsalida,$hentrada,$id_veh)
	{
		$query=$this->db->query("
		select avm.id_vehiculo
		from tcm_solicitud_transporte as st
		inner join tcm_asignacion_sol_veh_mot as avm
		on (st.id_solicitud_transporte=avm.id_solicitud_transporte)
		where st.fecha_mision='$fecha' and 
		(
			(st.hora_salida>='$hsalida' and st.hora_salida<='$hentrada')
			 or (st.hora_entrada>='$hsalida' and st.hora_entrada<='$hentrada')
			 or (st.hora_salida<='$hsalida' and st.hora_entrada>='$hentrada')
		)
		and st.estado_solicitud_transporte=3
		and avm.id_vehiculo='$id_veh'
		");
		
		if($query->num_rows() > 0)
		{
			return $query->result();
		}
		else
		{
			return 0;
		}
	}
	
	///////////////////////////////////////////////////////////////////////////////////
	
	//////////////////////////////////Informacion del cuadro de dialogo de aprobacion de solicitudes////////////////////////////////////////////////
	function datos_de_solicitudes($id,$seccion){
		/*
		*	Cambie este query porque el id_seccion 
		*	que esta recibiendo esta funcion es el 
		*	del usuario logueado, no el del solicitante,
		*	por eso no mostraba la seccion real a la que pertenece
		*	el empleado solicitante
		*/

		$query=$this->db->query("SELECT id_solicitud_transporte id, 
								LOWER(CONCAT_WS(' ',e.primer_nombre, e.segundo_nombre, e.tercer_nombre, e.primer_apellido,e.segundo_apellido,e.apellido_casada)) AS nombre,
								DATE_FORMAT(fecha_solicitud_transporte, '%d-%m-%Y') fechaS,
								DATE_FORMAT(fecha_mision, '%d-%m-%Y')  fechaM,
								DATE_FORMAT(hora_salida,'%h:%i %p') salida,
								DATE_FORMAT(hora_entrada,'%h:%i %p') entrada,
								LOWER(COALESCE(nombre_seccion, 'No hay registro')) seccion,
								requiere_motorista req,
								acompanante,
								id_empleado_solicitante
								FROM tcm_solicitud_transporte  s 
								INNER JOIN sir_empleado e ON id_empleado_solicitante = id_empleado
								LEFT JOIN sir_empleado_informacion_laboral i ON e.id_empleado = i.id_empleado
								LEFT JOIN org_seccion sec ON i.id_seccion = sec.id_seccion
								WHERE id_solicitud_transporte=".$id);
		return $query->result();
	}	

	function aprobar($id_solicitud_transporte, $estado, $id_usuario){     /// puede ser aprobar o denegar
		$q="UPDATE tcm_solicitud_transporte SET 
				id_empleado_autoriza= (SELECT id_empleado FROM org_usuario u INNER JOIN sir_empleado e ON u.nr = e.nr WHERE id_usuario = '".$id_usuario."'),
				estado_solicitud_transporte = $estado,
				id_usuario_modifica = '".$id_usuario."', 
				fecha_modificacion=  CONCAT_WS(' ', CURDATE(),CURTIME()),  
				fecha_aprobacion=  CONCAT_WS(' ', CURDATE(),CURTIME())  
			WHERE id_solicitud_transporte= ".$id_solicitud_transporte;
				
		  $query=$this->db->query($q);
	
		return $query;
	}
	
	/////////////////////////////////CONSULTAR VEHÍCULOS//////////////////////////////////////////
	function consultar_vehiculos($estado=NULL, $id=NULL, $mtto=NULL)
	{
		$where="";
		
		if($estado==NULL)
		{
			if($id!=NULL) $where=" where v.id_vehiculo='$id'";
			
			$consulta="
			select v.id_vehiculo id, v.placa, v.estado, vm.nombre as marca, vmo.modelo, vc.nombre_clase clase, vcon.condicion,
			coalesce(max(tvk.km_final),0) as km_actual, coalesce(mtto.dif_km,0) as dif_km, mtto.fecha_recepcion, mtto.id_ingreso_taller
			from tcm_vehiculo as v
			inner join tcm_vehiculo_marca as vm on (v.id_marca=vm.id_vehiculo_marca)
			inner join tcm_vehiculo_modelo as vmo on (v.id_modelo=vmo.id_vehiculo_modelo)
			inner join tcm_vehiculo_clase as vc on (v.id_clase=vc.id_vehiculo_clase)
			inner join tcm_vehiculo_condicion as vcon on (v.id_condicion=vcon.id_vehiculo_condicion)
			left join tcm_vehiculo_kilometraje as tvk on (tvk.id_vehiculo=v.id_vehiculo)
			left join (
						select max(tit.id_ingreso_taller) as id_ingreso_taller, tit.id_vehiculo, (tit.kilometraje_ingreso+5000) as km_mtto,
						coalesce(max(tvk.km_final),0) as km_actual, ((tit.kilometraje_ingreso+5000)-coalesce(max(tvk.km_final),0)) as dif_km,
						date_format(tit.fecha_recepcion,'%d-%m-%Y') AS fecha_recepcion
						from tcm_ingreso_taller as tit
						left join tcm_vehiculo_kilometraje as tvk on (tit.id_vehiculo=tvk.id_vehiculo)
						group by tit.id_vehiculo
					  ) as mtto on (v.id_vehiculo=mtto.id_vehiculo)
			".$where."
			group by v.id_vehiculo";
		}
		else
		{
			if($id!=NULL) $where=" where v.estado='$estado' and v.id_vehiculo='$id'";
			elseif($mtto!=NULL) $where=" where (v.estado<='$estado' and v.estado>0)";
			else $where=" where v.estado='$estado'";
			
			$consulta="
			select v.id_vehiculo id, v.placa, v.estado, vm.nombre as marca, vmo.modelo, vc.nombre_clase clase, vcon.condicion,
			coalesce(max(tvk.km_final),0) as km_actual, coalesce(mtto.dif_km,0) as dif_km, mtto.fecha_recepcion, mtto.id_ingreso_taller
			from tcm_vehiculo as v
			inner join tcm_vehiculo_marca as vm on (v.id_marca=vm.id_vehiculo_marca)
			inner join tcm_vehiculo_modelo as vmo on (v.id_modelo=vmo.id_vehiculo_modelo)
			inner join tcm_vehiculo_clase as vc on (v.id_clase=vc.id_vehiculo_clase)
			inner join tcm_vehiculo_condicion as vcon on (v.id_condicion=vcon.id_vehiculo_condicion)
			left join tcm_vehiculo_kilometraje as tvk on (tvk.id_vehiculo=v.id_vehiculo)
			left join (
						select max(tit.id_ingreso_taller) as id_ingreso_taller, tit.id_vehiculo, (tit.kilometraje_ingreso+5000) as km_mtto,
						coalesce(max(tvk.km_final),0) as km_actual, ((tit.kilometraje_ingreso+5000)-coalesce(max(tvk.km_final),0)) as dif_km,
						date_format(tit.fecha_recepcion,'%d-%m-%Y') AS fecha_recepcion
						from tcm_ingreso_taller as tit
						left join tcm_vehiculo_kilometraje as tvk on (tit.id_vehiculo=tvk.id_vehiculo)
					  	group by tit.id_vehiculo
					  ) as mtto on (v.id_vehiculo=mtto.id_vehiculo)
			".$where."
			group by v.id_vehiculo";
		}
		$query=$this->db->query($consulta);
		return $query->result();
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////
	
	
		
	//////////////////////////////CONSULTAR MARCAS//////////////////////////////
	function consultar_marcas($id_marca=NULL)
	{
		$where="";
		if($id_marca!=NULL) $where=" where id_vehiculo_marca=".$id_marca;
		$query=$this->db->query("select id_vehiculo_marca, lower(nombre) as nombre  from tcm_vehiculo_marca ".$where);
		return $query->result();
	}
	////////////////////////////////////////////////////////////////////////////
	
	//////////////////////////////REGISTRAR MARCAS//////////////////////////////
	function nueva_marca($marca)
	{
		$query=$this->db->query("insert into tcm_vehiculo_marca(nombre) values('$marca')");
		$query2=$this->db->query("select max(id_vehiculo_marca) as id from tcm_vehiculo_marca");
		
		$vm=$query2->result();
		foreach($vm as $v)
		{
			$id=$v->id;
		}
		return $id;
	}
	////////////////////////////////////////////////////////////////////////////
	
	//////////////////////////////CONSUlTAR MODElOS//////////////////////////////
	function consultar_modelos()
	{
		$query=$this->db->query("select id_vehiculo_modelo, lower(modelo) as modelo from tcm_vehiculo_modelo");
		return $query->result();
	}
	////////////////////////////////////////////////////////////////////////////
	
	//////////////////////////////REGISTRAR MODELOS//////////////////////////////
	function nuevo_modelo($modelo)
	{
		$query=$this->db->query("insert into tcm_vehiculo_modelo(modelo) values('$modelo')");
		$query2=$this->db->query("select max(id_vehiculo_modelo) as id from tcm_vehiculo_modelo");
		
		$vm=$query2->result();
		foreach($vm as $v)
		{
			$id=$v->id;
		}
		return $id;
	}
	////////////////////////////////////////////////////////////////////////////
	
	//////////////////////////////CONSUlTAR ClASES//////////////////////////////
	function consultar_clases()
	{
		$query=$this->db->query("select id_vehiculo_clase, lower(nombre_clase) as nombre_clase from tcm_vehiculo_clase");
		return $query->result();
	}
	////////////////////////////////////////////////////////////////////////////////
	
	//////////////////////////////REGISTRAR CLASES//////////////////////////////
	function nueva_clase($clase)
	{
		$query=$this->db->query("insert into tcm_vehiculo_clase(nombre_clase) values('$clase')");
		$query2=$this->db->query("select max(id_vehiculo_clase) as id from tcm_vehiculo_clase");
		
		$vm=$query2->result();
		foreach($vm as $v)
		{
			$id=$v->id;
		}
		return $id;
	}
	////////////////////////////////////////////////////////////////////////////
	
	//////////////////////////////CONSUlTAR CONDICIONES//////////////////////////////
	function consultar_condiciones()
	{
		$query=$this->db->query("select id_vehiculo_condicion, lower(condicion) as condicion from tcm_vehiculo_condicion");
		return $query->result();
	}
	////////////////////////////////////////////////////////////////////////////
	
	//////////////////////////////CONSULTAR SECCIONES//////////////////////////////
	function consultar_secciones()
	{
		$query=$this->db->query("select id_seccion, lower(nombre_seccion) as seccion from org_seccion");
		return $query->result();
	}
	////////////////////////////////////////////////////////////////////////////
	
	/////////////////CONSULTAR LAS FUENTES DE FONDO DE LOS VEHÍCULOS//////////////////
	
	function consultar_fuente_fondo()
	{
		$query=$this->db->query("select id_fuente_fondo,nombre_fuente_fondo as fuente from tcm_fuente_fondo");
		return $query->result();
	}
	
	//////////////////////////////////////////////////////////////////////////////////
	
	//////////////////////////////REGISTRAR FUENTES DE FONDO//////////////////////////////
	function nueva_fuente($fuente)
	{
		$query=$this->db->query("insert into tcm_fuente_fondo(nombre_fuente_fondo) values('$fuente')");
		$query2=$this->db->query("select max(id_fuente_fondo) as id from tcm_fuente_fondo");
		
		$vm=$query2->result();
		foreach($vm as $v)
		{
			$id=$v->id;
		}
		return $id;
	}
	////////////////////////////////////////////////////////////////////////////
	
	function consultar_vehiculo_taller($id, $estado=NULL)
	{
		$where="";
		if($estado!=NULL)
		{
			$where=" and v.estado='$estado'";
		}
		$query=$this->db->query("
		select v.placa, IF(vmot.id_empleado=0,'No tiene asignado',LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,
		s.segundo_apellido, s.apellido_casada))) AS motorista, o.nombre_seccion as seccion, vm.nombre as marca, vmo.modelo, vc.nombre_clase clase, vcon.condicion,
		COALESCE(max(vk.km_final),'0') as kilometraje, v.anio, v.estado, ff.nombre_fuente_fondo as fuente_fondo,v.imagen, v.id_seccion, v.id_clase, v.id_condicion,
		v.id_fuente_fondo, v.id_marca, v.id_modelo, v.id_vehiculo, vmot.id_empleado, v.tipo_combustible, COALESCE(taller.id_ingreso_taller,'0') AS id_ingreso_taller
		from tcm_vehiculo as v
		inner join tcm_vehiculo_marca as vm on (v.id_marca=vm.id_vehiculo_marca)
		inner join tcm_vehiculo_modelo as vmo on (v.id_modelo=vmo.id_vehiculo_modelo)
		inner join tcm_vehiculo_clase as vc on (v.id_clase=vc.id_vehiculo_clase)
		inner join tcm_vehiculo_condicion as vcon on (v.id_condicion=vcon.id_vehiculo_condicion)
		left join tcm_vehiculo_motorista as vmot on (v.id_vehiculo=vmot.id_vehiculo)
		left join tcm_vehiculo_kilometraje as vk on (vk.id_vehiculo=v.id_vehiculo)
		left join sir_empleado as s on (vmot.id_empleado=s.id_empleado)
		inner join org_seccion as o on (v.id_seccion=o.id_seccion)
		inner join tcm_fuente_fondo as ff on (ff.id_fuente_fondo=v.id_fuente_fondo)
		LEFT JOIN 
		(
			SELECT id_ingreso_taller, id_vehiculo
			from tcm_ingreso_taller
			where id_vehiculo='$id' and (IF(COALESCE(fecha_entrega,'false')='false',TRUE,FALSE))
		) AS taller ON (v.id_vehiculo=taller.id_vehiculo)
		where v.id_vehiculo='$id' ".$where."
		GROUP BY v.id_vehiculo
		");
		return $query->result();
	}
	
	////////////////////////////función para la ventana de vehiculo_info para el caso de los vehículos con id_taller_interno//////
	
	function consultar_vehiculo_taller2($id, $estado=NULL)
	{
		$where="";
		if($estado!=NULL) $where="and v.estado='$estado'";
		
		$query=$this->db->query("
			select v.placa, IF(vmot.id_empleado=0,'No tiene asignado',LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,
			s.segundo_apellido, s.apellido_casada))) AS motorista, o.nombre_seccion as seccion, vm.nombre as marca, vmo.modelo, vc.nombre_clase clase, vcon.condicion,
			COALESCE(max(vk.km_final),'0') as kilometraje, v.anio, v.estado, ff.nombre_fuente_fondo as fuente_fondo,v.imagen, v.id_seccion, v.id_clase, v.id_condicion,
			v.id_fuente_fondo, v.id_marca, v.id_modelo, v.id_vehiculo, vmot.id_empleado, v.tipo_combustible, coalesce(it.id_ingreso_taller,'0') as id_ingreso_taller
			from tcm_vehiculo as v
			inner join tcm_vehiculo_marca as vm on (v.id_marca=vm.id_vehiculo_marca)
			inner join tcm_vehiculo_modelo as vmo on (v.id_modelo=vmo.id_vehiculo_modelo)
			inner join tcm_vehiculo_clase as vc on (v.id_clase=vc.id_vehiculo_clase)
			inner join tcm_vehiculo_condicion as vcon on (v.id_condicion=vcon.id_vehiculo_condicion)
			left join tcm_vehiculo_motorista as vmot on (v.id_vehiculo=vmot.id_vehiculo)
			left join tcm_vehiculo_kilometraje as vk on (vk.id_vehiculo=v.id_vehiculo)
			left join sir_empleado as s on (vmot.id_empleado=s.id_empleado)
			inner join org_seccion as o on (v.id_seccion=o.id_seccion)
			inner join tcm_fuente_fondo as ff on (ff.id_fuente_fondo=v.id_fuente_fondo)
			left join tcm_ingreso_taller as it on (v.id_vehiculo=it.id_vehiculo)
			where v.id_vehiculo='$id' AND (IF(COALESCE(it.fecha_entrega,'false')='false',TRUE,FALSE)) ".$where."
			GROUP BY v.placa,motorista,seccion,marca,modelo,clase,condicion
			");
		
		return $query->result();
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	//////////////////////Consultar los datos de los vehículos//////////////////
	function consultar_datos_vehiculos($id)
	{
		$query=$this->db->query("
		select v.placa, IF(vmot.id_empleado=0,'No tiene asignado',LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido,s.apellido_casada))) AS motorista,
		o.nombre_seccion as seccion, vm.nombre as marca, vmo.modelo, vc.nombre_clase clase, vcon.condicion, COALESCE(max(vk.km_final),'0') as kilometraje, v.anio, v.estado,
		ff.nombre_fuente_fondo as fuente_fondo,v.imagen, v.id_seccion, v.id_clase, v.id_condicion, v.id_fuente_fondo, v.id_marca, v.id_modelo, v.id_vehiculo, vmot.id_empleado
		from tcm_vehiculo as v
		inner join tcm_vehiculo_marca as vm on (v.id_marca=vm.id_vehiculo_marca)
		inner join tcm_vehiculo_modelo as vmo on (v.id_modelo=vmo.id_vehiculo_modelo)
		inner join tcm_vehiculo_clase as vc on (v.id_clase=vc.id_vehiculo_clase)
		inner join tcm_vehiculo_condicion as vcon on (v.id_condicion=vcon.id_vehiculo_condicion)
		left join tcm_vehiculo_motorista as vmot on (v.id_vehiculo=vmot.id_vehiculo)
		left join tcm_vehiculo_kilometraje as vk on (vk.id_vehiculo=v.id_vehiculo)
		left join sir_empleado as s on (vmot.id_empleado=s.id_empleado)
		inner join org_seccion as o on (v.id_seccion=o.id_seccion)
		inner join tcm_fuente_fondo as ff on (ff.id_fuente_fondo=v.id_fuente_fondo)
		where v.id_vehiculo='$id' and v.estado=1;
		GROUP BY v.placa,motorista,seccion,marca,modelo,clase,condicion
		");
		return $query->result();
	}
	
	//////////////////////////FUNCIÓN PARA REGISTRAR UN NUEVO VEHÍCULO/////////////////////////////
	
	function registrar_vehiculo($nplaca,$marca,$modelo,$clase,$year,$condicion,$tcombustible,$seccion,$motorista,$fuente_fondo,$foto)
	{
		$query="INSERT INTO tcm_vehiculo(placa,id_seccion,id_marca,id_modelo,id_clase,id_condicion,anio,imagen,id_fuente_fondo,estado,cantidad_combustible,id_seccion_vale,tipo_combustible)
				values('$nplaca','$seccion','$marca','$modelo','$clase','$condicion','$year','$foto','$fuente_fondo',1,50,'$seccion','$tcombustible')";
		$this->db->query($query);
		
		$q=$this->db->query("select max(id_vehiculo) as id from tcm_vehiculo");
		$vehiculo=$q->result();
		
		foreach($vehiculo as $v)
		{
			$id_vehiculo=$v->id;
		}
		
		$query2="INSERT INTO tcm_vehiculo_motorista(id_empleado,id_vehiculo) values('$motorista','$id_vehiculo')";
		$this->db->query($query2);
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	function modificar_vehiculo($nplaca,$marca,$modelo,$clase,$year,$condicion,$tcombustible,$seccion,$motorista,$fuente_fondo,$foto,$id_vehiculo,$estado)
	{
		$query="UPDATE tcm_vehiculo SET placa='$nplaca', id_seccion='$seccion', id_marca='$marca', id_modelo='$modelo', id_clase='$clase', id_condicion='$condicion', anio='$year', imagen='$foto', id_fuente_fondo='$fuente_fondo', estado='$estado', tipo_combustible='$tcombustible' WHERE (id_vehiculo='$id_vehiculo');";
		$this->db->query($query);
				
		$query2="update tcm_vehiculo_motorista set id_empleado='$motorista' where id_vehiculo='$id_vehiculo'";
		$this->db->query($query2);
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	function filtro_vehiculo($datos)
	{
		extract($datos);
		
		$where='';
		$select='';
		
		/*///////////////declaración de los where, por defecto vacios//////////*/
		$where_placa='';
		$where_marca='';
		$where_modelo='';
		$where_clase='';
		$where_condicion='';
		$where_anio='';
		$where_fuente_fondo='';
		$where_estado='';
		$where_tipo_combustible='';
		$where_seccion='';
		
		
		/*/////////////////////verificación de los select y los where para generar la consulta///////////*/
		if($marca!='') $where_marca = " vm.id_vehiculo_marca = '".$marca."' ";
		else $select = $select.', vm.nombre as marca';
		
		if($modelo!='') $where_modelo = " vmo.id_vehiculo_modelo = '".$modelo."' ";
		else $select = $select.', vmo.modelo';
		
		if($clase!='') $where_clase = " vc.id_vehiculo_clase = '".$clase."' ";
		else $select = $select.', vc.nombre_clase as clase';
		
		if($condicion!='') $where_condicion = " vcon.id_vehiculo_condicion = '".$condicion."' ";
		else $select = $select.', vcon.condicion';
		
		if($condicion!='') $where_condicion = " vcon.id_vehiculo_condicion = '".$condicion."' ";
		else $select = $select.', vcon.condicion';
		
		if($condicion!='') $where_condicion = " vcon.id_vehiculo_condicion = '".$condicion."' ";
		else $select = $select.', vcon.condicion';
		
		if($condicion!='') $where_condicion = " vcon.id_vehiculo_condicion = '".$condicion."' ";
		else $select = $select.', vcon.condicion';
		
		if($condicion!='') $where_condicion = " vcon.id_vehiculo_condicion = '".$condicion."' ";
		else $select = $select.', vcon.condicion';
		
		if($condicion!='') $where_condicion = " vcon.id_vehiculo_condicion = '".$condicion."' ";
		else $select = $select.', vcon.condicion';
		
		if($condicion!='') $where_condicion = " vcon.id_vehiculo_condicion = '".$condicion."' ";
		else $select = $select.', vcon.condicion';
		
		/*/////////////concatenación de los where/////////////*/
		$cont=0;
		if($where_marca=='' && $where_modelo=='' && $where_clase=='' && $where_condicion=='') $where='';
		else
		{
			$where='where (';
			
			if($where_marca!='')
			{
				$where=$where.$where_marca;
				$cont++;
			}
			if($where_modelo!='')
			{
				if($cont==0)
				{
					$where=$where.$where_modelo;
					$cont++;
				}
				else $where=$where.' and '.$where_modelo;
			}
			if($where_clase!='')
			{
				if($cont==0)
				{
					$where=$where.$where_clase;
					$cont++;
				}
				else $where=$where.' and '.$where_clase;
			}
			if($where_condicion!='')
			{
				if($cont==0)
				{
					$where=$where.$where_condicion;
					$cont++;
				}
				else $where=$where.' and '.$where_condicion;
			}
			
			$where=$where.' )';
		}
		
		/*////////////////////concatenación de la consulta////////////////*/
		$consulta="select v.placa ".$select
			." from tcm_vehiculo as v
			inner join tcm_vehiculo_marca as vm on (v.id_marca=vm.id_vehiculo_marca)
			inner join tcm_vehiculo_modelo as vmo on (v.id_modelo=vmo.id_vehiculo_modelo)
			inner join tcm_vehiculo_clase as vc on (v.id_clase=vc.id_vehiculo_clase)
			inner join tcm_vehiculo_condicion as vcon on (v.id_condicion=vcon.id_vehiculo_condicion) ".$where.";";

		$query=$this->db->query($consulta);
		return $query->result_array();
		/*if($query->num_rows()>0) {
			return $query->result_array();
		}
		else {
			return 0;
		}*/
	}
	
	///////////////////////////////FUNCIÓN QUE FIlTRA lOS INFORMES DE SOlICITUDES/////////////////////
	
	function filtro_informes($id_solicitante,$id_user,$seccion,$estado,$id_mot,$placa,$f1,$f2,$h1,$h2)
	{
		$select="";
		$where="";
		
		if($id_solicitante==0)
		{
			$select=$select.", e.";
		}
		
		$query="
		select sol.id_solicitud_transporte ".$select."
		from tcm_solicitud_transporte as sol
		inner join tcm_asignacion_sol_veh_mot as asv on (sol.id_solicitud_transporte=asv.id_solicitud_transporte)
		inner join tcm_empleado as e on (e.id_empleado=sol.id_empleado_solicitante)
		inner join org_usuario
		";
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////////

	///////////////////////////FUNCIÓN PARA VALIDAR LA FECHA Y LA HORA DE UNA SOLICITUD//////////
	
	function validar_fecha_hora()
	{
		$query=$this->db->query("select st.id_solicitud_transporte,st.fecha_mision, st.hora_salida, st.hora_entrada, avm.id_vehiculo from tcm_solicitud_transporte as st
inner join tcm_asignacion_sol_veh_mot as avm on (st.id_solicitud_transporte=avm.id_solicitud_transporte)");
		return $query->result();
	}
	//////////////////////////////////////////////////////////////////////////////////////////////
	
	///////////////CONSULTAR MOTORISTAS: CARGA LOS MOTORISTAS CORRESPONDIENTES A LOS VEHICULOS VERSION OFICINAS/////////////////////////////////
	function consultar_motoristas($id,$seccion)
	{
		$query=$this->db->query("(SELECT t.id_empleado, IF(t.id_empleado!=0,LOWER(CONCAT_WS(' ',e.primer_nombre, e.segundo_nombre, e.tercer_nombre, e.primer_apellido, e.segundo_apellido, e.apellido_casada)),'No tiene asignado') as nombre FROM tcm_vehiculo_motorista t left join sir_empleado e on (t.id_empleado=e.id_empleado)
where t.id_vehiculo='$id') union (SELECT t.id_empleado,LOWER(CONCAT_WS(' ',e.primer_nombre, e.segundo_nombre, e.tercer_nombre, e.primer_apellido, e.segundo_apellido, e.apellido_casada)) AS nombre FROM tcm_vehiculo_motorista t
inner join sir_empleado e on (t.id_empleado=e.id_empleado)
inner join tcm_vehiculo v on (t.id_vehiculo=v.id_vehiculo)
where (v.id_seccion='$seccion')
order by e.primer_nombre ASC);");
		return $query->result();
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	///////////////CONSULTAR MOTORISTAS: CARGA LOS MOTORISTAS CORRESPONDIENTES A LOS VEHICULOS VERSION CENTRAL/////////////////////////////////
	function consultar_motoristas_central($id,$seccion=NULL)
	{
		$query=$this->db->query("(SELECT t.id_empleado, IF(t.id_empleado!=0,LOWER(CONCAT_WS(' ',e.primer_nombre, e.segundo_nombre, e.tercer_nombre, e.primer_apellido, e.segundo_apellido, e.apellido_casada)),'No tiene asignado') as nombre FROM tcm_vehiculo_motorista t left join sir_empleado e on (t.id_empleado=e.id_empleado)
where t.id_vehiculo='$id') union (SELECT t.id_empleado,LOWER(CONCAT_WS(' ',e.primer_nombre, e.segundo_nombre, e.tercer_nombre, e.primer_apellido, e.segundo_apellido, e.apellido_casada)) AS nombre FROM tcm_vehiculo_motorista t
inner join sir_empleado e on (t.id_empleado=e.id_empleado)
inner join tcm_vehiculo v on (t.id_vehiculo=v.id_vehiculo)
where (v.id_seccion!=52 and v.id_seccion!=53 and v.id_seccion!=54 and v.id_seccion!=55 and v.id_seccion!=56 and v.id_seccion!=57 and v.id_seccion!=58 and v.id_seccion!=59 and v.id_seccion!=60 and v.id_seccion!=61 and v.id_seccion!=64 and v.id_seccion!=65 and v.id_seccion!=66)
order by e.primer_nombre ASC);");
		return $query->result();
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	///////////////CONSULTAR MOTORISTAS: CARGA LOS MOTORISTAS CORRESPONDIENTES A LOS VEHICULOS VERSION ADMIN/////////////////////////////////
	function consultar_motoristas_nacional($id)
	{
		$query=$this->db->query("(SELECT t.id_empleado, IF(t.id_empleado!=0,LOWER(CONCAT_WS(' ',e.primer_nombre, e.segundo_nombre, e.tercer_nombre, e.primer_apellido, e.segundo_apellido, e.apellido_casada)),'No tiene asignado') as nombre FROM tcm_vehiculo_motorista t left join sir_empleado e on (t.id_empleado=e.id_empleado)
where t.id_vehiculo='$id') union (SELECT t.id_empleado,LOWER(CONCAT_WS(' ',e.primer_nombre, e.segundo_nombre, e.tercer_nombre, e.primer_apellido, e.segundo_apellido, e.apellido_casada)) AS nombre FROM tcm_vehiculo_motorista t
inner join sir_empleado e on (t.id_empleado=e.id_empleado)
inner join tcm_vehiculo v on (t.id_vehiculo=v.id_vehiculo)
order by e.primer_nombre ASC);");
		return $query->result();
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	////////////CONSULTAR MOTORISTAS 2: CARGA LOS EMPLEADOS CUYO CARGO NOMINAL O FUNCIONAL ES MOTORISTA////////////
	// 130717 se agrego AND s.id_estado = 1 para limitar a empleados activos
	function consultar_motoristas2($jefe_transporte=NULL)
	{
		$incluir_jefe="";
		if($jefe_transporte!=NULL)/*Si la variable es distinta de NULL, en el resultado se incluirá la jefe de transporte*/
		{
			$incluir_jefe=" || i.id_cargo_funcional=159 ";			
		}
		$query=$this->db->query("select
		s.id_empleado,
		LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido,s.apellido_casada)) AS nombre
		from sir_empleado as s
		inner join sir_empleado_informacion_laboral as i on (i.id_empleado=s.id_empleado)
		inner join sir_cargo_funcional as c on (c.id_cargo_funcional=i.id_cargo_funcional)
		inner join sir_cargo_nominal as n on (n.id_cargo_nominal=i.id_cargo_nominal)
		where
			(i.id_cargo_funcional=291 || i.id_cargo_nominal=101 || i.id_cargo_nominal=102 || i.id_cargo_nominal=103 ".$incluir_jefe.")
			AND s.id_estado = 1
			and i.id_empleado_informacion_laboral in
			(SELECT max(id_empleado_informacion_laboral) as id
				FROM sir_empleado_informacion_laboral
				group by id_empleado
				having count(id_empleado)>=1)");
		return $query->result();
	}
	
	///////////////////////////////////////////////////
	
	function acompanantes_internos($id)
	{
		$query=$this->db->query("SELECT t.id_empleado AS id_empleado, e.nr, UPPER(CONCAT_WS(' ',e.primer_nombre, e.segundo_nombre, e.tercer_nombre, e.primer_apellido,e.segundo_apellido,e.apellido_casada)) as nombre FROM sir_empleado e inner join tcm_acompanante t on (e.id_empleado=t.id_empleado)
where t.id_solicitud_transporte='$id';");
		
		return $query->result();
	}
	
	/////////////////FUNCIÓN PARA INSERTAR UN REGISTRO DE ASIGNACIÓN DE VEHICULO Y MOTORISTA/////////////////////////////
	function asignar_veh_mot($id_solicitud,$id_empleado,$id_vehiculo,$estado,$fecha,$nr,$id_usuario)
	{
		$query=$this->db->query("insert into tcm_asignacion_sol_veh_mot(id_solicitud_transporte,id_empleado,id_vehiculo,id_empleado_asigna,fecha_hora_asignacion) values('$id_solicitud','$id_empleado','$id_vehiculo','$id_usuario','$fecha')");
		
		$query2=$this->db->query("update tcm_solicitud_transporte set estado_solicitud_transporte='$estado', fecha_modificacion='$fecha', id_usuario_modifica='$id_usuario' where id_solicitud_transporte='$id_solicitud'");
		
		return $query;
	}
	
	//////////////////FUNCIÓN PARA REGISTRAR UNA DENEGACIÓN DE ASIGNACIÓN DE VEHICULO Y MOTORISTA//////////////////////
	function nasignar_veh_mot($id_solicitud,$estado,$fecha,$id_usuario)
	{
		$query=$this->db->query("update tcm_solicitud_transporte set estado_solicitud_transporte='$estado', fecha_modificacion='$fecha', id_usuario_modifica='$id_usuario' where id_solicitud_transporte='$id_solicitud'");
		
		return $query;
	}
	////////////////////////////FUNCION DE DESTINOS/////////////////
	function destinos($id)
	{
		$query=$this->db->query("select UPPER(m.municipio) AS municipio, upper(d.lugar_destino) destino, upper(d.mision_encomendada) mision, upper(direccion_destino) as direccion from tcm_destino_mision as d
inner join org_municipio as m on (d.id_municipio=m.id_municipio)
inner join tcm_solicitud_transporte as s on (d.id_solicitud_transporte=s.id_solicitud_transporte)
where s.id_solicitud_transporte='$id'");
		
		return $query->result();
	}
	///////////////////////////////////////////////////////////////
	
	/////////////////////////////////////////////////
	function info_adicional($id_empleado=0)
	{		
		$sentencia="SELECT 
					tcm_empleado.nr,
					tcm_empleado.id_seccion,
					UPPER(tcm_empleado.funcional) AS funcional,
					UPPER(tcm_empleado.nominal) AS nominal,
					UPPER(tcm_empleado.seccion) AS nivel_1,
					UPPER(tcm_empleado.padre) AS nivel_2,
					UPPER(tcm_empleado.abuelo) AS nivel_3
					FROM tcm_empleado
					WHERE tcm_empleado.id_empleado='".$id_empleado."'";
		$query=$this->db->query($sentencia);
	
		if($query->num_rows() > 0) {
			return $query->row_array();
		}
		else {
			return array(
				'nr' => 0
			);
		}
	}
	
	function guardar_solicitud($formuInfo) 
	{
		extract($formuInfo);
		$sentencia="INSERT INTO tcm_solicitud_transporte
					(id_solicitud_transporte, fecha_solicitud_transporte, id_empleado_solicitante, fecha_mision, hora_salida, hora_entrada, correo_cc, requiere_motorista, acompanante, id_usuario_crea, fecha_creacion, estado_solicitud_transporte) 
					VALUES 
					($id_solicitud_transporte, '$fecha_solicitud_transporte', '$id_empleado_solicitante', '$fecha_mision', '$hora_salida', '$hora_entrada', '$correo_cc', '$requiere_motorista', '$acompanante', '$id_usuario_crea', '$fecha_creacion', '$estado_solicitud_transporte')";
		$this->db->query($sentencia);
		return $this->db->insert_id();
	}
	
	function guardar_acompanantes($formuInfo) 
	{
		extract($formuInfo);
		$sentencia="INSERT INTO tcm_acompanante
					(id_solicitud_transporte, id_empleado) 
					VALUES 
					('$id_solicitud_transporte', '$id_empleado')";
		$this->db->query($sentencia);
	}
	
	function insertar_descripcion($id,$descrip,$quien=NULL)
	{
		$q="INSERT INTO tcm_observacion 
				(id_solicitud_transporte, observacion, quien_realiza)
			VALUES
				('".$id."', 
				'".$descrip."', 
				".$quien."
				);";
		
		$query=$this->db->query($q);	
		return $query;
	}
	
	function consultar_empleados_seccion($id_seccion)
	{
		$sentencia="SELECT DISTINCT
							LOWER(CONCAT_WS(' ',e.primer_nombre, e.segundo_nombre, e.tercer_nombre, e.primer_apellido, e.segundo_apellido, e.apellido_casada)) AS nombre,
							i.id_empleado AS NR
							FROM
							sir_empleado_informacion_laboral AS i
							LEFT JOIN sir_empleado AS e ON e.id_empleado = i.id_empleado
							WHERE (i.id_empleado, i.fecha_inicio) IN  
							( SELECT id_empleado ,MAX(fecha_inicio)  FROM sir_empleado_informacion_laboral GROUP BY id_empleado  ) 
							AND i.id_seccion=".$id_seccion."
							ORDER BY e.id_empleado";


		$query=$this->db->query($sentencia);
		if($query->num_rows() > 0) {
			return $query->result_array();
		}
		else {
			return 0;
		}
	}
	
/*---------------------------------Control de salidas y entradas de Vehiculos------------------------------------*/
	function salidas_entradas_vehiculos(){
		$query=$this->db->query("SELECT s.id_solicitud_transporte id,
				LOWER(CONCAT_WS(' ',e.primer_nombre, e.segundo_nombre, e.tercer_nombre,
				e.primer_apellido,e.segundo_apellido,e.apellido_casada)) AS nombre,
				estado_solicitud_transporte estado,
				DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
				fecha_mision,
				DATE_FORMAT(hora_salida,'%h:%i %p') salida,
				DATE_FORMAT(hora_entrada,'%h:%i %p') entrada,
				vh.placa	
			FROM tcm_solicitud_transporte  s 
			INNER JOIN sir_empleado e ON id_empleado_solicitante = id_empleado
			INNER JOIN  tcm_asignacion_sol_veh_mot asi ON asi.id_solicitud_transporte=s.id_solicitud_transporte
			INNER JOIN tcm_vehiculo vh ON vh.id_vehiculo= asi.id_vehiculo
			WHERE  ( (estado_solicitud_transporte=3) OR estado_solicitud_transporte=4) ORDER BY fecha_mision DESC LIMIT 100");
		return $query->result();
		
		}
function salidas_entradas_vehiculos_seccion($id_seccion){
		$query=$this->db->query("
						SELECT * FROM
						(
						SELECT t.id_solicitud_transporte id,
						DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
						DATE_FORMAT(hora_entrada,'%r') entrada,
						DATE_FORMAT(hora_salida,'%r') salida,
						veh.placa,
						estado_solicitud_transporte estado,
						LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
						LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido, s.apellido_casada)) AS nombre
						FROM tcm_solicitud_transporte  t
						INNER JOIN tcm_asignacion_sol_veh_mot asi  ON asi.id_solicitud_transporte = t.id_solicitud_transporte
						INNER JOIN tcm_vehiculo veh ON veh.id_vehiculo = asi.id_vehiculo
						LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
						LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
						LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)

						WHERE 
							(estado_solicitud_transporte= 3 OR estado_solicitud_transporte = 4 )
							and o.id_seccion= '".$id_seccion."'

						)
						as k
						GROUP BY k.id 
						ORDER BY k.fecha DESC

			");
		return $query->result();
		
		}
function salidas_entradas_vehiculos_SanSalvador(){
		$query=$this->db->query("
SELECT * FROM
(
SELECT t.id_solicitud_transporte id,
DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
DATE_FORMAT(hora_entrada,'%r') entrada,
DATE_FORMAT(hora_salida,'%r') salida,
veh.placa,
estado_solicitud_transporte estado,
LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido, s.apellido_casada)) AS nombre
FROM tcm_solicitud_transporte  t
INNER JOIN tcm_asignacion_sol_veh_mot asi  ON asi.id_solicitud_transporte = t.id_solicitud_transporte
INNER JOIN tcm_vehiculo veh ON veh.id_vehiculo = asi.id_vehiculo
LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)

WHERE 
	(estado_solicitud_transporte= 3 OR estado_solicitud_transporte = 4 )
	and o.id_seccion  NOT IN 
			(SELECT id_seccion FROM org_seccion WHERE id_seccion BETWEEN 52 AND 66)
)
as k
GROUP BY k.id 
ORDER BY k.fecha DESC

			");
		return $query->result();
		
		}


	function accesoriosABordo($id_solicitud)
{
	$sentencia=" SELECT
			ca.id_solicitud_transporte,
			a.nombre, a.id_accesorio
				FROM
					tcm_accesorios  a
				INNER JOIN	tcm_chekeo_accesorio ca ON a.id_accesorio=ca.id_accesorio
				WHERE ca.id_solicitud_transporte = '$id_solicitud'";
	$query=$this->db->query($sentencia);
	return $query->result();
}


function salida_vehiculo($id, $km_inicial,$hora_salida,$acces,$gas){
			
		$q="INSERT INTO tcm_vehiculo_kilometraje (
					id_solicitud_transporte, 
					id_vehiculo, 
					km_inicial,
					km_final, 
					hora_salida, 
					fecha_modificacion,
					combustibleIni)
				VALUES(
					'".$id."', 
					(SELECT id_vehiculo FROM tcm_asignacion_sol_veh_mot WHERE id_solicitud_transporte = ".$id."),
					 '".$km_inicial."',
					 '".$km_inicial."',        
					CONCAT_WS(' ',CURDATE(),'".$hora_salida."'), 
					CONCAT_WS(' ',CURDATE(),CURTIME()),
					'".$gas."'
				);";
		$this->db->query($q);

				foreach($acces as $row)://insert de accesorio


			$this->db->query("INSERT INTO tcm_chekeo_accesorio(id_solicitud_transporte, 
							id_accesorio, salida)
							VALUES
							($id, $row, 1 );");
			
		endforeach;
		
		$q="UPDATE tcm_solicitud_transporte SET
		estado_solicitud_transporte = '4' 
		WHERE	id_solicitud_transporte = '$id' ;";
		$this->db->query($q);
	}


function regreso_vehiculo($id, $km, $hora, $gas,$acces){

	$q="UPDATE tcm_vehiculo_kilometraje 
	SET
		km_final = '$km' , 
		combustible = '$gas' , 
		hora_entrada = CONCAT(CURDATE(),' $hora'), 
		fecha_modificacion = CURDATE()
	WHERE
		id_solicitud_transporte = '$id' ;
		";
	
		$this->db->query($q);
		
		foreach($acces as $row)://insert de accesorio

		$this->db->query("UPDATE tcm_chekeo_accesorio SET regreso = 1	
				WHERE id_solicitud_transporte = $id AND id_accesorio = $row ;"); 

			
		endforeach;
		$q="
		UPDATE tcm_solicitud_transporte SET
		estado_solicitud_transporte = '5'  
		WHERE	id_solicitud_transporte = '$id' ;
		";
		$this->db->query($q);
		
	}
function infoSolicitud($id){
	$query="
SELECT
	LOWER(
		CONCAT_WS(
			' ',
			e.primer_nombre,
			e.segundo_nombre,
			e.tercer_nombre,
			e.primer_apellido,
			e.segundo_apellido,
			e.apellido_casada
		)
	) AS nombre,
	DATE_FORMAT(hora_salida, '%h:%i %p') salida,
	DATE_FORMAT(hora_entrada, '%h:%i %p') regreso,
	v.placa,
	v.id_vehiculo,
	vm.modelo,
v.cantidad_combustible as gas
FROM
	tcm_vehiculo v
INNER JOIN tcm_asignacion_sol_veh_mot asi ON v.id_vehiculo = asi.id_vehiculo
INNER JOIN tcm_vehiculo_modelo vm ON vm.id_vehiculo_modelo = v.id_modelo
INNER JOIN tcm_solicitud_transporte s ON s.id_solicitud_transporte = asi.id_solicitud_transporte
LEFT JOIN sir_empleado e ON e.id_empleado = s.id_empleado_solicitante
		WHERE s.id_solicitud_transporte = ".$id;
		$q=$this->db->query($query);
		return $q->result();
	}
	function buscar_solicitudes($id_empleado=NULL,$estado=NULL, $id_seccion=NULL)
	{

	
		$whereExtra="";

		if($id_empleado!=NULL) {
			$whereExtra.=" AND id_empleado_solicitante='".$id_empleado."'  ";
	
		}
		if($estado!=""||$estado!=NULL){
				$whereExtra.=" AND estado_solicitud_transporte>='".$estado."' "  ;
		}
		if($id_seccion!=NULL){
				$whereExtra.=" AND i.id_seccion= '".$id_seccion."' "  ;
				}


		
		$sentencia="
		SELECT * FROM
		(
			SELECT id_solicitud_transporte id,
			DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
			fecha_mision,
			DATE_FORMAT(hora_entrada,'%r') entrada,
			DATE_FORMAT(hora_salida,'%r') salida,
			requiere_motorista,
			LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
			estado_solicitud_transporte estado,
			LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido, s.apellido_casada)) AS nombre
			FROM tcm_solicitud_transporte  t
			LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
			LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
			LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)
			WHERE 
				(t.id_empleado_solicitante not in
						(select id_empleado from sir_empleado_informacion_laboral))
				".$whereExtra."
					
			UNION
					
			SELECT id_solicitud_transporte id,
			DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
			fecha_mision,
			DATE_FORMAT(hora_entrada,'%r') entrada,
			DATE_FORMAT(hora_salida,'%r') salida,
			requiere_motorista,
			LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
			estado_solicitud_transporte estado,
			LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido,s.apellido_casada)) AS nombre
			FROM tcm_solicitud_transporte  t
			LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
			LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
			LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)
			WHERE 
				(	i.id_empleado_informacion_laboral in
					(
						SELECT se.id_empleado_informacion_laboral
						FROM sir_empleado_informacion_laboral AS se
						INNER JOIN
						(
							SELECT id_empleado, MAX(fecha_inicio) AS fecha FROM sir_empleado_informacion_laboral
							GROUP BY id_empleado
							HAVING COUNT(id_empleado>=1)
						)
						AS ids
						ON (se.id_empleado=ids.id_empleado AND se.fecha_inicio=ids.fecha)
						ORDER BY se.id_empleado_informacion_laboral
					)
				)
				".$whereExtra."
		)
		as k
		GROUP BY k.id ORDER BY k.fecha_mision DESC LIMIT 100 
		";

		//echo $sentencia;
		
		$query=$this->db->query($sentencia);
		if($query->num_rows() > 0) {
			return $query->result_array();
		}
		else {
			return $query->result_array();
		}
	}
	
	function consultar_destinos($id_solicitud=NULL)
	{
		$sentencia="SELECT lugar_destino, direccion_destino, mision_encomendada, LOWER(CONCAT_WS(', ',departamento,municipio)) AS lugar, tcm_destino_mision.id_municipio FROM tcm_destino_mision INNER JOIN org_municipio ON org_municipio.id_municipio = tcm_destino_mision.id_municipio INNER JOIN org_departamento ON org_departamento.id_departamento = org_municipio.id_departamento_pais WHERE id_solicitud_transporte='".$id_solicitud."'";
		$query=$this->db->query($sentencia);
		return $query->result_array();
	}
	
	function eliminar_solicitud($id_solicitud, $real=false)
	{
		if ($real) {
			$sentencia="DELETE FROM tcm_solicitud_transporte where id_solicitud_transporte='$id_solicitud'";
		} else {
			$sentencia="UPDATE tcm_solicitud_transporte SET estado_solicitud_transporte='-1' 
								WHERE id_solicitud_transporte='$id_solicitud'";
		}
		
		$query=$this->db->query($sentencia);

	}

	function consultar_estado($id_solicitud=NULL)
	{
		$sentencia="SELECT estado_solicitud_transporte  as estado FROM tcm_solicitud_transporte  WHERE id_solicitud_transporte='$id_solicitud'";
		$query=$this->db->query($sentencia);
		return $query->result_array();

	}
	function consultar_solicitud($id_solicitud=NULL,$estado=NULL)
	{
		if($estado!=NULL)
			$where_estado=" AND tcm_solicitud_transporte.estado_solicitud_transporte IN(".$estado.")";
		$sentencia="SELECT
					tcm_solicitud_transporte.id_solicitud_transporte,
					tcm_solicitud_transporte.id_empleado_solicitante,
					tcm_solicitud_transporte.id_empleado_autoriza,
					DATE_FORMAT(tcm_solicitud_transporte.fecha_aprobacion, '%d/%m/%Y') AS fecha_aprobacion,
					DATE_FORMAT(tcm_solicitud_transporte.fecha_aprobacion,'%h:%i %p') AS hora_aprobacion,
					UPPER(CONCAT_WS(' ',e1.primer_nombre, e1.segundo_nombre, e1.tercer_nombre, e1.primer_apellido, e1.segundo_apellido, e1.apellido_casada)) AS nombre,
					UPPER(CONCAT_WS(' ',e2.primer_nombre, e2.segundo_nombre, e2.tercer_nombre, e2.primer_apellido, e2.segundo_apellido, e2.apellido_casada)) AS nombre2,
					DATE_FORMAT(tcm_solicitud_transporte.fecha_mision, '%d/%m/%Y') AS fecha_mision,
					DATE_FORMAT(tcm_solicitud_transporte.fecha_solicitud_transporte, '%d/%m/%Y') AS fecha_solicitud_transporte,
					DATE_FORMAT(tcm_solicitud_transporte.fecha_creacion, '%d/%m/%Y %h:%i %p') AS fecha_creacion,
					DATE_FORMAT(tcm_solicitud_transporte.hora_salida,'%h:%i %p') AS hora_salida,
					DATE_FORMAT(tcm_solicitud_transporte.hora_entrada,'%h:%i %p') AS hora_entrada,
					tcm_solicitud_transporte.acompanante,
					tcm_observacion.observacion,
					tcm_observacion.quien_realiza,
					tcm_solicitud_transporte.requiere_motorista,
					tcm_solicitud_transporte.estado_solicitud_transporte,
					e1.nr AS NR
					FROM tcm_solicitud_transporte
					INNER JOIN sir_empleado AS e1 ON tcm_solicitud_transporte.id_empleado_solicitante=e1.id_empleado
					LEFT JOIN sir_empleado AS e2 ON tcm_solicitud_transporte.id_empleado_autoriza=e2.id_empleado
					LEFT JOIN tcm_observacion ON tcm_observacion.id_solicitud_transporte = tcm_solicitud_transporte.id_solicitud_transporte
					WHERE tcm_solicitud_transporte.id_solicitud_transporte='".$id_solicitud."' ".$where_estado;
		$query=$this->db->query($sentencia);
		if($query->num_rows() > 0) {
			return $query->row_array();
		}
		else {
			return array(
				'id_solicitud_transporte' => 0,
				'id_empleado_solicitante' => 0,
				'fecha_mision' => "",
				'hora_salida' => "",
				'hora_entrada' => "",
				'acompanante' => "",
				'id_municipio' => 0,
				'lugar_destino' => "",
				'mision_encomendada' => "",
				'nombre' => "",
				'observacion' => "",
				'quien_realiza' => "",
				'requiere_motorista' => "",
				'NR' => "",
				'estado_solicitud_transporte' => ""
			);
		}
	}
	function kilometraje($id){
			$query="SELECT v.id_vehiculo, COALESCE(MAX(k.km_inicial), 0) AS KMinicial, COALESCE(MAX(k.km_Final), 0) AS KMFinal
				FROM tcm_vehiculo  v 
				LEFT JOIN tcm_vehiculo_kilometraje  k  
				ON  v.id_vehiculo= k.id_vehiculo 
				GROUP BY v.id_vehiculo HAVING v.id_vehiculo=".$id;
		$q=$this->db->query($query);
		return $q->result();

	}

	function guardar_destinos($formuInfo) 
	{
		extract($formuInfo);
		$sentencia="INSERT INTO tcm_destino_mision
					(id_solicitud_transporte, id_municipio, lugar_destino, direccion_destino, mision_encomendada) 
					VALUES 
					('$id_solicitud_transporte', '$id_municipio', '$lugar_destino', '$direccion_destino', '$mision_encomendada')";
		$this->db->query($sentencia);
	}
		
	function accesorios(){
					$query="SELECT 	id_accesorio, nombre,  descrip, estado	 
							FROM  tcm_accesorios ";
							
		$q=$this->db->query($query);
		return $q->result();
		
		}
	function datos_motorista_vehiculo($id_solicitud_transporte) 
	{
		$sentencia="SELECT
					UPPER(CONCAT_WS(' ',e1.primer_nombre, e1.segundo_nombre, e1.tercer_nombre, e1.primer_apellido, e1.segundo_apellido, e1.apellido_casada)) AS nombre,
					tcm_asignacion_sol_veh_mot.id_empleado_asigna,
					DATE_FORMAT(tcm_asignacion_sol_veh_mot.fecha_hora_asignacion, '%d/%m/%Y') AS fecha_asignacion,
					DATE_FORMAT(tcm_asignacion_sol_veh_mot.fecha_hora_asignacion,'%h:%i %p') AS hora_asignacion,
					UPPER(CONCAT_WS(' ',e2.primer_nombre, e2.segundo_nombre, e2.tercer_nombre, e2.primer_apellido, e2.segundo_apellido, e2.apellido_casada)) AS nombre2,
					tcm_vehiculo.placa,
					UPPER(tcm_vehiculo_clase.nombre_clase) AS nombre_clase
					FROM tcm_vehiculo
					INNER JOIN tcm_vehiculo_clase ON tcm_vehiculo_clase.id_vehiculo_clase = tcm_vehiculo.id_clase
					INNER JOIN tcm_asignacion_sol_veh_mot ON tcm_asignacion_sol_veh_mot.id_vehiculo = tcm_vehiculo.id_vehiculo
					INNER JOIN sir_empleado AS e1 ON tcm_asignacion_sol_veh_mot.id_empleado = e1.id_empleado
					INNER JOIN sir_empleado AS e2 ON tcm_asignacion_sol_veh_mot.id_empleado_asigna = e2.id_empleado
					WHERE tcm_asignacion_sol_veh_mot.id_solicitud_transporte = '".$id_solicitud_transporte."'";
		$query=$this->db->query($sentencia);
		
		return $query->row_array();
	}
	function datos_salida_entrada_real($id_solicitud_transporte)
	{
		$sentencia="SELECT
					CONCAT_WS(' ', tcm_vehiculo_kilometraje.km_inicial, 'Kms') AS km_inicial,
					CASE 
						WHEN tcm_vehiculo_kilometraje.km_final IS NOT NULL THEN
							CONCAT_WS(' ', tcm_vehiculo_kilometraje.km_final, 'Kms') 
						ELSE
							''
						END
						AS km_final,
					CASE 
						WHEN tcm_vehiculo_kilometraje.km_final IS NOT NULL THEN
					CONCAT_WS(' ', (tcm_vehiculo_kilometraje.km_final-tcm_vehiculo_kilometraje.km_inicial), 'Kms')
						ELSE
							''
						END
						AS total,
					tcm_vehiculo_kilometraje.combustible,
					DATE_FORMAT(tcm_vehiculo_kilometraje.hora_salida,'%d/%m/%Y') AS fecha_mision,
					DATE_FORMAT(tcm_vehiculo_kilometraje.hora_salida,'%h:%i %p') AS hora_salida,
					DATE_FORMAT(tcm_vehiculo_kilometraje.hora_entrada,'%h:%i %p') AS hora_entrada
					FROM tcm_vehiculo_kilometraje
					WHERE id_solicitud_transporte='".$id_solicitud_transporte."'";
		$query=$this->db->query($sentencia);
		
		return $query->row_array();
	}
	function observaciones($id_solicitud_transporte)
	{
		$sentencia="SELECT
					tcm_observacion.observacion,
					tcm_observacion.quien_realiza
					FROM tcm_observacion
					WHERE id_solicitud_transporte='".$id_solicitud_transporte."'";
		$query=$this->db->query($sentencia);
		
		return $query->result_array();
	}

	public function is_departamental($id_seccion=NULL)
	{	
		if($id_seccion>=52 AND $id_seccion<=66 ){
			return true;
		}else{
			return false;
		}
	}
	
	public function consultar_empleados_depto()
	{
		$sentencia="SELECT DISTINCT
					LOWER(CONCAT_WS(' ',e.primer_nombre, e.segundo_nombre, e.tercer_nombre, e.primer_apellido, e.segundo_apellido, e.apellido_casada)) AS nombre,
					i.id_empleado AS NR
					FROM
					sir_empleado_informacion_laboral AS i
					LEFT JOIN sir_empleado AS e ON e.id_empleado = i.id_empleado
					WHERE (i.id_empleado, i.fecha_inicio) IN  
						( SELECT id_empleado ,MAX(fecha_inicio)  FROM sir_empleado_informacion_laboral GROUP BY id_empleado  ) 
						AND i.id_seccion<>52 AND i.id_seccion<>53 AND i.id_seccion<>54 AND i.id_seccion<>55 AND i.id_seccion<>56 AND i.id_seccion<>57 AND i.id_seccion<>58 AND i.id_seccion<>59 AND i.id_seccion<>60 AND i.id_seccion<>61 AND i.id_seccion<>64 AND i.id_seccion<>65 AND i.id_seccion<>66 
						ORDER BY e.id_empleado";
		$query=$this->db->query($sentencia);

		return $query->result_array();
	}
	
	public function buscar_solicitudes_depto($estado=NULL)
	{
		
		$sentencia="SELECT * FROM
					(
						SELECT id_solicitud_transporte id,
						DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
						DATE_FORMAT(hora_entrada,'%r') entrada,
						DATE_FORMAT(hora_salida,'%r') salida,
						requiere_motorista,
						LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
						estado_solicitud_transporte estado,
						LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido, s.apellido_casada)) AS nombre
						FROM tcm_solicitud_transporte  t
						LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
						LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
						LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)
						WHERE 
							(estado_solicitud_transporte>='".$estado."' and (o.id_seccion!=52 and o.id_seccion!=53 and o.id_seccion!=54 and o.id_seccion!=55 and o.id_seccion!=56 and o.id_seccion!=57 and o.id_seccion!=58 and o.id_seccion!=59 and o.id_seccion!=60 and o.id_seccion!=61 and o.id_seccion!=64 and o.id_seccion!=65 and o.id_seccion!=66))
							and (t.id_empleado_solicitante not in
									(select id_empleado from sir_empleado_informacion_laboral))
								
						UNION
								
						SELECT id_solicitud_transporte id,
						DATE_FORMAT(fecha_mision,'%d-%m-%Y') fecha,
						DATE_FORMAT(hora_entrada,'%r') entrada,
						DATE_FORMAT(hora_salida,'%r') salida,
						requiere_motorista,
						LOWER(COALESCE(o.nombre_seccion, 'No hay registro')) seccion,
						estado_solicitud_transporte estado,
						LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido,s.apellido_casada)) AS nombre
						FROM tcm_solicitud_transporte  t
						LEFT JOIN sir_empleado s ON (s.id_empleado=t.id_empleado_solicitante)
						LEFT JOIN sir_empleado_informacion_laboral i ON (i.id_empleado=s.id_empleado)
						LEFT JOIN org_seccion o ON (i.id_seccion=o.id_seccion)
						WHERE 
							(estado_solicitud_transporte>='".$estado."' and (o.id_seccion!=52 and o.id_seccion!=53 and o.id_seccion!=54 and o.id_seccion!=55 and o.id_seccion!=56 and o.id_seccion!=57 and o.id_seccion!=58 and o.id_seccion!=59 and o.id_seccion!=60 and o.id_seccion!=61 and o.id_seccion!=64 and o.id_seccion!=65 and o.id_seccion!=66))
							and 
							(	i.id_empleado_informacion_laboral in
								(
									SELECT se.id_empleado_informacion_laboral
									FROM sir_empleado_informacion_laboral AS se
									INNER JOIN
									(
										SELECT id_empleado, MAX(fecha_inicio) AS fecha FROM sir_empleado_informacion_laboral
										GROUP BY id_empleado
										HAVING COUNT(id_empleado>=1)
									)
									AS ids
									ON (se.id_empleado=ids.id_empleado AND se.fecha_inicio=ids.fecha)
									ORDER BY se.id_empleado_informacion_laboral
								)
							)
					)
					as k
					GROUP BY k.id";
		$query=$this->db->query($sentencia);

		return $query->result_array();
	}

	function update_combustible($id, $gas)
	{
		$q="UPDATE tcm_vehiculo SET cantidad_combustible= '".$gas."'
		 WHERE (id_vehiculo='(SELECT id_vehiculo 
		 	FROM tcm_asignacion_sol_veh_mot 
		 	WHERE id_solicitud_transporte = ".$id.")')";
		
		$query=$this->db->query($q);

	}
	

	function consultar_direfencia($id,$gas)
	{

		$q="SELECT
				cantidad_combustible - ".$gas." as diferencia
			FROM tcm_vehiculo
			WHERE
				id_vehiculo = ( SELECT id_vehiculo
					FROM 		tcm_asignacion_sol_veh_mot
					WHERE
						id_solicitud_transporte = ".$id."
				)";
		
		$query=$this->db->query($q);
		return $query->result_array();
	}


	function insertar_bitacora_combustible($id,$gas)
	{

		$q="INSERT INTO `tcm_bitacora_vehiculo` (
				`id_solicitud_transporte_bitacora`,
				`fecha_hora`,
				`id_vehiculo`,
				`diferencia`
			)
			VALUES
				(
					".$id.",
				 CONCAT_WS(' ',CURDATE(),CURTIME()),
					(SELECT id_vehiculo 
					 	FROM tcm_asignacion_sol_veh_mot 
					 	WHERE id_solicitud_transporte = ".$id."),
					'".$gas."'
				)";
		
		$query=$this->db->query($q);
	}
	///////////////////////FUNCIONES DEL PRESUPUESTO////////////////////////////////////////////////////
	
	/**********************FUNCIÓN PRESUPUESTO ASIGNADO PARA EL ÁREA DE MANTENIMIENTO VEHICULAR***********************/
	function presupuesto($id_presupuesto=NULL)
	{
		$where="";
		if($id_presupuesto!=NULL) $where=" where tpm.id_presupuesto='$id_presupuesto'";
		
		$query="SELECT tpm.id_presupuesto, tpm.activo, COALESCE((tpm.presupuesto+r.refuerzo), tpm.presupuesto) as presupuesto, 
				COALESCE((tpm.presupuesto+COALESCE(r.refuerzo,0))-g.gasto, (tpm.presupuesto+COALESCE(r.refuerzo,0))) as cantidad_actual,
				COALESCE(g.gasto,0) as gasto,
				DATE_FORMAT(tpm.fecha_inicial,'%d-%m-%Y') AS fecha_inicial,
				DATE_FORMAT(tpm.fecha_final,'%d-%m-%Y') AS fecha_final
				FROM tcm_presupuesto_mantenimiento AS tpm
				LEFT JOIN 
				(
					SELECT tpm.id_presupuesto, SUM(gp.gasto) AS gasto
					FROM tcm_gasto_presupuesto AS gp
					INNER JOIN tcm_presupuesto_mantenimiento AS tpm ON (gp.id_presupuesto=tpm.id_presupuesto)
					GROUP BY tpm.id_presupuesto
				) as g
				ON (tpm.id_presupuesto=g.id_presupuesto)
				LEFT JOIN 
				(
					SELECT tpm.id_presupuesto, SUM(rp.refuerzo) AS refuerzo
					FROM tcm_refuerzo_presupuesto AS rp
					INNER JOIN tcm_presupuesto_mantenimiento AS tpm ON (rp.id_presupuesto=tpm.id_presupuesto)
					GROUP BY tpm.id_presupuesto
				) as r
				ON (tpm.id_presupuesto=r.id_presupuesto)".$where;
				
		$query=$this->db->query($query);
		return $query->result_array();
	}
	/*****************************************************************************************************************/
	
	/******************FUNCIÓN QUE OBTIENE LOS GASTOS REALIZADOS DE UN PRESUPUESTO EN ESPECÍFICO**********************/
	function gastos($id_presupuesto)
	{
		$query="SELECT tcp.id_presupuesto, tcp.id_gasto, DATE_FORMAT(tcp.fecha,'%d-%m-%Y') AS fecha, tcp.gasto, tcp.descripcion
				FROM tcm_gasto_presupuesto AS tcp
				INNER JOIN tcm_presupuesto_mantenimiento AS tpm ON (tcp.id_presupuesto=tpm.id_presupuesto)
				WHERE tpm.id_presupuesto='$id_presupuesto'";
				
		$query=$this->db->query($query);
		return $query->result_array();
	}
	/*****************************************************************************************************************/
	
	/*******************************FUNCIÓN PARA GUARDAR UN REGISTRO PRESUPUESTO**************************************/
	function guardar_presupuesto($datos)
	{
		extract($datos);
		$fecha_inicial2=date('Y-m-d',strtotime($fecha_inicial));
		$fecha_final2=date('Y-m-d',strtotime($fecha_final));
		$fecha_crea=date('Y-m-d H:i:s');
		$q="update tcm_presupuesto_mantenimiento set activo=0 where activo=1"; /*Se valida que no hayan otros presupuestos activos*/
		$this->db->query($q);
		/*Se inserta el nuevo presupuesto*/
		$query="INSERT INTO tcm_presupuesto_mantenimiento(presupuesto,fecha_inicial,fecha_final,activo,id_usuario_crea,fecha_creacion)
				values('$presupuesto','$fecha_inicial2','$fecha_final2', '1','$id_usuario','$fecha_crea')";
		return $this->db->query($query);
	}
	/*****************************************************************************************************************/
	
	/*******************************FUNCIÓN PARA MODIFICAR UN REGISTRO PRESUPUESTO**************************************/
	function modificar_presupuesto($datos)
	{
		extract($datos);
		$fecha_inicial2=date('Y-m-d',strtotime($fecha_inicial));
		$fecha_final2=date('Y-m-d',strtotime($fecha_final));
		$fecha_modifica=date('Y-m-d H:i:s');
		$query="UPDATE tcm_presupuesto_mantenimiento SET presupuesto='$presupuesto', fecha_inicial='$fecha_inicial2', fecha_final='$fecha_final2', id_usuario_modifica='$id_usuario', fecha_modifica='$fecha_modifica' WHERE id_presupuesto='".$_POST['id_presupuesto']."';";
		return $this->db->query($query);
	}
	/*****************************************************************************************************************/
	
	/*******************************FUNCIÓN PARA GUARDAR UN REFUERZO A UN PRESUPUESTO**************************************/
	function guardar_refuerzo($datos)
	{
		extract($datos);
		$fecha_crea=date('Y-m-d H:i:s');
		$query="INSERT INTO tcm_refuerzo_presupuesto(refuerzo,id_presupuesto,justificacion,id_usuario_crea,fecha_creacion)
				values('$refuerzo','$id_presupuesto','$justificacion','$id_usuario','$fecha_crea')";
		return $this->db->query($query);
	}
	/*****************************************************************************************************************/
	
	/******************FUNCIÓN QUE OBTIENE LOS REFUERZOS REALIZADOS DE UN PRESUPUESTO EN ESPECÍFICO**********************/
	function refuerzos($id_presupuesto)
	{
		$query="SELECT tfp.justificacion, DATE_FORMAT(tfp.fecha_creacion,'%d-%m-&Y') AS fecha,
				tfp.refuerzo
				FROM tcm_refuerzo_presupuesto AS tfp
				INNER JOIN tcm_presupuesto_mantenimiento AS tpm ON (tfp.id_presupuesto=tpm.id_presupuesto)
				WHERE tfp.id_presupuesto='$id_presupuesto'";
				
		$query=$this->db->query($query);
		return $query->result_array();
	}
	/*****************************************************************************************************************/
	
	/**********************FUNCIÓN QUE RETORNA El PRESUPUESTO QUE SE ENCUENTRA ACTIVO**********************************/
	function presupuesto_activo()
	{
		$query="select id_presupuesto, presupuesto from tcm_presupuesto_mantenimiento where activo=1";
		$query=$this->db->query($query);
		return $query->result_array();
	}
	/******************************************************************************************************************/
	/*******************************************FUNCIÓN PARA OBTENER EL PRESUPUESTO DE MTTO********************************************/
	function presupuesto_mtto($datos)
	{
		extract($datos);
		
		$where_fecha="";
		$where_fecha2="";
		$where_vehiculo="";
		$group_by_vehiculo="";
		
		//////////////////FILTRO DE FECHAS/////////////////////
		if($fecha_inicial!='' && $fecha_final!='')
		{
			$fecha_inicial2=date('Y-m-d',strtotime($fecha_inicial));
			$fecha_final2=date('Y-m-d',strtotime($fecha_final));
			$where_fecha="(itx.fecha_entrega between '$fecha_inicial2' and '$fecha_final2')";
			$where_fecha2="(tta.fecha between '$fecha_inicial2' and '$fecha_final2')";
		}
		/*elseif($fecha_inicial=='' && $fecha_final=='')
		{
			$fecha_inicial=date('Y')."-01-01";
			$fecha_final=date('Y')."-12-31";
			$where_fecha="where (titx.fecha_entrega between '$fecha_inicial' and '$fecha_final')";
		}*/
		elseif($fecha_inicial!='')
		{
			$fecha_inicial2=date('Y-m-d',strtotime($fecha_inicial));
			$where_fecha="(itx.fecha_entrega='$fecha_inicial2')";
			$where_fecha2="(tta.fecha='$fecha_inicial2')";
		}
		elseif($fecha_final!='')
		{
			$fecha_final2=date('Y-m-d',strtotime($fecha_final));
			$where_fecha="(itx.fecha_entrega='$fecha_final2')";
			$where_fecha2="(tta.fecha='$fecha_final2')";
		}
		
		if($mtto!=0 && $mtto!="")
		{
			if($id_vehiculo!=0 && $id_vehiculo!="")
			{
				$where_vehiculo="and (v.id_vehiculo='$id_vehiculo' or v2.id_vehiculo='$id_vehiculo')";
				$group_by_vehiculo=",v.id_vehiculo";
			}
			if($mtto==1)
			{
				$query="SELECT round(SUM(interno.gasto_estimado),2) AS gasto, tpm.id_presupuesto, tpm.presupuesto
							FROM
							(
								SELECT tab.nombre, tab.cantidad, tab.precio_promedio, (tab.cantidad*tab.precio_promedio) AS gasto_estimado,
								tum.unidad_medida, IF((COALESCE(v.placa,'0')!='0'),v.placa,v2.placa) as placa
								FROM tcm_articulo_bodega as tab
								INNER JOIN tcm_unidad_medida AS tum on (tab.id_unidad_medida=tum.id_unidad_medida)
								INNER JOIN tcm_transaccion_articulo AS tta ON (tab.id_articulo=tta.id_articulo)
								LEFT JOIN tcm_mantenimiento_interno AS tmi ON (tmi.id_mantenimiento_interno=tta.id_mantenimiento_interno)
								LEFT JOIN tcm_mantenimiento_rutinario AS tmr ON (tmr.id_mantenimiento_rutinario=tta.id_mantenimiento_rutinario)
								LEFT JOIN tcm_ingreso_taller AS tit ON (tmi.id_ingreso_taller=tit.id_ingreso_taller)
								LEFT JOIN tcm_vehiculo AS v ON (tit.id_vehiculo=v.id_vehiculo)
								LEFT JOIN tcm_vehiculo AS v2 ON (v2.id_vehiculo=tmr.id_vehiculo)
								WHERE (tta.tipo_transaccion like 'salida') ".$where_vehiculo."
							) AS interno, tcm_presupuesto_mantenimiento as tpm
							GROUP BY tpm.id_presupuesto";
			}
			else
			{
				if ($where_fecha!="") {
					$where_fecha = 'WHERE '.$where_fecha;
					if($id_vehiculo!=0 && $id_vehiculo!="") $where_vehiculo=" AND (tit.id_vehiculo='$id_vehiculo') ";
				}
				if($id_vehiculo!=0 && $id_vehiculo!="") $where_vehiculo="WHERE (tit.id_vehiculo='$id_vehiculo') ";
				
				$query="SELECT tpm.id_presupuesto, ROUND(SUM(COALESCE(exter.gasto,'0')),2) AS gasto, ROUND(COALESCE(tpm.presupuesto,'0'),2) AS presupuesto
						FROM tcm_presupuesto_mantenimiento AS tpm
						LEFT JOIN
						(
							SELECT tit.id_vehiculo, tgp.id_presupuesto, SUM(tgp.gasto) AS gasto
							FROM tcm_ingreso_taller_ext AS itx
							INNER JOIN tcm_gasto_presupuesto AS tgp ON (tgp.id_gasto=itx.id_gasto)
							INNER JOIN tcm_ingreso_taller AS tit ON (itx.id_ingreso_taller=tit.id_ingreso_taller)
							WHERE (itx.fecha_entrega BETWEEN '2015-08-20' AND '2015-09-30') AND tit.id_vehiculo=82
							GROUP BY tit.id_vehiculo, tgp.id_presupuesto
						) AS exter ON (exter.id_presupuesto=tpm.id_presupuesto)
						GROUP BY tpm.id_presupuesto";
			}
		}
		else
		{
			if($id_vehiculo!=0 && $id_vehiculo!="")
			{
				$query="SELECT ROUND(mtto_interno.gasto_interno,2) as gasto_interno, ROUND(mtto_externo.gasto_externo,2) AS gasto_externo,
						DATE_FORMAT(mtto_externo.fecha,'%d-%m-%Y') AS fecha, mtto_interno.placa, mtto_externo.presupuesto,mtto_externo.id_presupuesto,
						ROUND((mtto_interno.gasto_interno+mtto_externo.gasto_externo),2) as total_gasto
						FROM 
						(
							SELECT tpm.id_presupuesto, tpm.presupuesto, sum(tgp.gasto) AS gasto_externo,titx.fecha_entrega AS fecha
							FROM tcm_gasto_presupuesto AS tgp
							INNER JOIN tcm_presupuesto_mantenimiento AS tpm ON (tpm.id_presupuesto=tgp.id_presupuesto)
							INNER JOIN tcm_ingreso_taller_ext as titx on (titx.id_gasto=tgp.id_gasto)
							".$where_fecha."
							GROUP BY tpm.id_presupuesto
						) AS mtto_externo,
						(
							SELECT SUM(interno.gasto_estimado) AS gasto_interno, interno.placa
							FROM
							(
								SELECT tab.nombre, tab.cantidad, tab.precio_promedio, (tab.cantidad*tab.precio_promedio) AS gasto_estimado,
								tum.unidad_medida, IF((COALESCE(v.placa,'0')!='0'),v.placa,v2.placa) as placa
								FROM tcm_articulo_bodega as tab
								INNER JOIN tcm_unidad_medida AS tum on (tab.id_unidad_medida=tum.id_unidad_medida)
								INNER JOIN tcm_transaccion_articulo AS tta ON (tab.id_articulo=tta.id_articulo)
								LEFT JOIN tcm_mantenimiento_interno AS tmi ON (tmi.id_mantenimiento_interno=tta.id_mantenimiento_interno)
								LEFT JOIN tcm_mantenimiento_rutinario AS tmr ON (tmr.id_mantenimiento_rutinario=tta.id_mantenimiento_rutinario)
								LEFT JOIN tcm_ingreso_taller AS tit ON (tmi.id_ingreso_taller=tit.id_ingreso_taller)
								LEFT JOIN tcm_vehiculo AS v ON (tit.id_vehiculo=v.id_vehiculo)
								LEFT JOIN tcm_vehiculo AS v2 ON (v2.id_vehiculo=tmr.id_vehiculo)
								where v.id_vehiculo='$id_vehiculo' or v2.id_vehiculo='$id_vehiculo'
							) AS interno
							GROUP BY interno.placa
						)AS mtto_interno";
			}
			else
			{
				if($where_fecha!="")
				{
					$where_fecha=' WHERE '.$where_fecha;
					$where_fecha2=' AND '.$where_fecha2;
				}
				
				$query=" SELECT tpm.id_presupuesto, ROUND(COALESCE(inter.gasto_interno,'0'),2) AS gasto_interno, ROUND(COALESCE(exter.gasto_externo,'0'),2) AS gasto_externo,
			ROUND(COALESCE(tpm.presupuesto,'0'),2) AS presupuesto, ROUND(COALESCE(inter.gasto_interno+exter.gasto_externo,'0'),2) AS total_gasto
						FROM tcm_presupuesto_mantenimiento AS tpm
						LEFT JOIN
						(
							SELECT interno.id_presupuesto, SUM(interno.gasto) AS gasto_interno
							FROM
							(
								SELECT ROUND((SUM(tta.cantidad)*tab.precio_promedio),2) AS gasto, tta.id_presupuesto
								FROM tcm_transaccion_articulo AS tta
								INNER JOIN tcm_articulo_bodega AS tab ON (tab.id_articulo=tta.id_articulo)
								INNER JOIN tcm_presupuesto_mantenimiento AS tpm ON (tpm.id_presupuesto=tta.id_presupuesto)
								WHERE tta.tipo_transaccion LIKE 'ENTRADA' ".$where_fecha2."
								GROUP BY tab.id_articulo
							)AS interno
							GROUP BY interno.id_presupuesto
						) AS inter ON (inter.id_presupuesto=tpm.id_presupuesto)
						LEFT JOIN
						(
							SELECT tgp.id_presupuesto, SUM(tgp.gasto) AS gasto_externo
							FROM tcm_ingreso_taller_ext AS itx
							INNER JOIN tcm_gasto_presupuesto AS tgp ON (tgp.id_gasto=itx.id_gasto)
							".$where_fecha."
							GROUP BY tgp.id_presupuesto
						) AS exter ON (exter.id_presupuesto=tpm.id_presupuesto)
						";
			}
		}
		$query=$this->db->query($query);
		return $query->result_array();
	}
	/*******************************************************************************************************************************************/
	
	/*******************************************FUNCIÓN PARA OBTENER EL ÚLTIMO ID_GASTO********************************************/
	function ultimo_id_gasto()
	{
		$query="select max(id_gasto) as id_gasto from tcm_gasto_presupuesto;";
		$query=$this->db->query($query);
		$id=$query->result_array();
		foreach($id as $i)
		{
			return $i['id_gasto'];
		}
	}
	/*******************************************************************************************************************************************/
	
	//////////////////////////////////////////////FUNCIONES DE LOS ARTÍCULOS////////////////////////////////////////////
	
	/************************************FUNCIÓN QUE MUESTRA LOS ARTÍCULOS DISPONIBLES*********************************/
	function inventario($id_articulo=NULL)
	{
		$where="";
		if($id_articulo!=NULL)
		{
			$where=" where art.id_articulo='$id_articulo'";
		}
		
		$query="SELECT art.id_articulo, art.nombre, art.id_unidad_medida, tum.unidad_medida, art.descripcion, art.cantidad, art.precio_promedio FROM tcm_articulo_bodega AS art
				INNER JOIN tcm_unidad_medida AS tum ON (art.id_unidad_medida=tum.id_unidad_medida) ".$where;
		$query=$this->db->query($query);
		return $query->result_array();
	}
	/******************************************************************************************************************/
	
	/************************************FUNCIÓN QUE MUESTRA LAS UNIDADES DE MEDIDA*********************************/
	function UM($id=NULL)
	{
		$where="";
		if($id!=NULL) $where=" where id_unidad_medida='$id'";
		$query="SELECT * FROM tcm_unidad_medida".$where;
		$query=$this->db->query($query);
		return $query->result_array();
	}
	/******************************************************************************************************************/
	
	/************************************FUNCIÓN QUE MUESTRA LAS TRANSACCIONES DE LOS ARTÍCULOS*********************************/
	function transaccion_articulo($id=NULL)
	{
		$fecha_inicial=date('Y')."-01-01";
		$fecha_final=date('Y')."-12-31";
		$fecha_inicial2=date('Y-m-d',strtotime($fecha_inicial));
		$fecha_final2=date('Y-m-d',strtotime($fecha_final));
		$where="";
		if($id!=NULL)
		{
			$where=" AND tta.id_articulo='$id'";
		}
		
		$query="SELECT tta.id_transaccion_articulo, tta.tipo_transaccion, tta.cantidad,  DATE_FORMAT(tta.fecha,'%d-%m-%Y') AS fecha,
				tta.id_articulo FROM tcm_transaccion_articulo as tta
				where tta.fecha between '$fecha_inicial2' and '$fecha_final2'
				".$where;
		$query=$this->db->query($query);
		return $query->result_array();
	}
	/******************************************************************************************************************/
	
	/*************************************FUNCIÓN PARA INGRESAR UN NUEVO ARTÍCULO**************************************/
		
	function guardar_articulo($datos)
	{
		extract($datos);
		$bandera=1;
		$fecha=date('Y-m-d');
		$precio_promedio=0.00;
		$UM=$this->UM($id_unidad_medida);
		
		foreach($UM as $u)
		{
			$unidad=$u['unidad_medida'];
		}
		
		$presupuesto=$this->presupuesto_activo();
		foreach($presupuesto as $p)
		{
			$id_presupuesto=$p['id_presupuesto'];
			$pre=$p['presupuesto'];
		}
		
		if($adquisicion=='comprado' && $gasto>0.00)
		{
			$gastos_a=$this->gastos($id_presupuesto);
			$gasto_s=0;
			foreach($gastos_a as $gastos_b)
			{
				$gasto_s=$gasto_s+$gastos_b['gasto'];
			}
			
			$cantidad_actual=$pre-$gasto_s;
			if($gasto<=$cantidad_actual)/*se valida que no se gaste más de lo que se dispone*/
			{
				$descripcion2="Se compraron ".$cantidad." ".$unidad." de ".$nombre;
				$query_gasto="INSERT INTO tcm_gasto_presupuesto (gasto, descripcion, fecha, id_presupuesto) VALUES ('$gasto', '$descripcion2', '$fecha', '$id_presupuesto');";
				
				if($this->db->query($query_gasto)) $bandera=$bandera*1;
				else $bandera=$bandera*0;
			}
			$precio_promedio=$gasto/$cantidad;
			$precio_promedio=number_format($precio_promedio,2);
		}
		
		if($bandera==1)
		{
			$query="INSERT INTO tcm_articulo_bodega (nombre, id_unidad_medida, descripcion, cantidad, precio_promedio) VALUES ('$nombre', '$id_unidad_medida', '$descripcion', '$cantidad','$precio_promedio');";
			if($this->db->query($query))
			{
				$id_articulo=$this->ultimo_id_articulo();
				$query2="INSERT INTO tcm_transaccion_articulo (tipo_transaccion, cantidad, fecha, id_articulo, id_presupuesto) VALUES ('ENTRADA', '$cantidad', '$fecha', '$id_articulo', '$id_presupuesto');";
				return $this->db->query($query2);
			}
		}
	}
	/********************************************************************************************************************************************/
	
	/*******************************************FUNCIÓN PARA OBTENER EL ÚLTIMO ID_ARTÍCULO********************************************/
	function ultimo_id_articulo()
	{
		$query="select max(id_articulo) as id_articulo from tcm_articulo_bodega;";
		$query=$this->db->query($query);
		$id=$query->result_array();
		foreach($id as $i)
		{
			return $i['id_articulo'];
		}
	}
	/*******************************************************************************************************************************************/
	
	/*******************************************FUNCIÓN PARA MODIFICAR LA INFORMACIÓN DEL ARTÍCULO********************************************/
	function modificar_articulo($datos)
	{
		extract($datos);
		$query="UPDATE tcm_articulo_bodega SET nombre='$nombre', id_unidad_medida='$id_unidad_medida', descripcion='$descripcion' WHERE (id_articulo='$id_articulo');";
		return $this->db->query($query);
	}
	/*******************************************************************************************************************************************/
	
	/*******************************************FUNCIÓN PARA CARGAR ARTÍCULO********************************************/
	function surtir_articulo($datos)
	{
		extract($datos);
		$bandera=1;
		$fecha=date('Y-m-d');
		$precio_promedio_act=0.00;
		$precio_promedio=0.00;
		$cant_art=0;
		$arti=$this->inventario($id_articulo);
		
		foreach($arti as $ar)
		{
			$precio_promedio_act=$ar['precio_promedio'];
			$cant_art=$ar['cantidad'];
		}		
		$precio_promedio_act=$precio_promedio_act*$cant_art;
		$precio_promedio=($gasto+$precio_promedio_act)/($cant_art+$cantidad);
		
		$presupuesto=$this->presupuesto_activo();
		foreach($presupuesto as $p)
		{
			$id_presupuesto=$p['id_presupuesto'];
			$pre=$p['presupuesto'];
		}
		
		if($adquisicion=='comprado' && $gasto>0.00)
		{
			$gastos_a=$this->gastos($id_presupuesto);
			$gasto_s=0;
			foreach($gastos_a as $gastos_b)
			{
				$gasto_s=$gasto_s+$gastos_b['gasto'];
			}
			
			$cantidad_actual=$pre-$gasto_s;
			if($gasto<=$cantidad_actual)/*se valida que no se gaste más de lo que se dispone*/
			{
				$descripcion2="Se compraron ".$cantidad." ".$unidad_medida." de ".$nombre2;
				$query_gasto="INSERT INTO tcm_gasto_presupuesto (gasto, descripcion, fecha, id_presupuesto) VALUES ('$gasto', '$descripcion2', '$fecha', '$id_presupuesto');";
				if($this->db->query($query_gasto))$bandera=$bandera*1;
				else $bandera=$bandera*0;
			}
		}
		
		if($bandera==1)
		{
			$cantidad_total=$cant_art+$cantidad;
			$precio_promedio=number_format($precio_promedio,2);
			$query="UPDATE tcm_articulo_bodega SET cantidad='$cantidad_total', precio_promedio='$precio_promedio' WHERE (id_articulo='$id_articulo');";
			if($this->db->query($query))
			{
				$query2="INSERT INTO tcm_transaccion_articulo (tipo_transaccion, cantidad, fecha, id_articulo, id_presupuesto) VALUES ('ENTRADA', '$cantidad', '$fecha', '$id_articulo', '$id_presupuesto');";
				return $this->db->query($query2);
			}
		}		
	}
	/*******************************************************************************************************************************************/
	
	/*******************************************FUNCIÓN PARA OBTENER EL REPORTE DEL KARDEX********************************************/
	function kardex_articulo($datos)
	{
		extract($datos);
		$where_fecha="";
		$where_vehiculo="";
		$where_articulo="";
		$select_vehiculo="";
		$select_articulo="";
		$group_by_articulo="";
		$group_by_vehiculo="";
		$aux=0;
		//print_r($datos);
		//////////////////FILTRO DE FECHAS/////////////////////
		if($fecha_inicial!='' && $fecha_final!='')
		{
			$fecha_inicial2=date('Y-m-d',strtotime($fecha_inicial));
			$fecha_final2=date('Y-m-d',strtotime($fecha_final));
			$where_fecha=" and (tta.fecha between '$fecha_inicial2' and '$fecha_final2')";
		}
		/*elseif($fecha_inicial=='' && $fecha_final=='')
		{
			$fecha_inicial=date('Y')."-01-01";
			$fecha_final=date('Y')."-12-31";
			$where_fecha="where (tta.fecha between '$fecha_inicial' and '$fecha_final')";
		}*/
		elseif($fecha_inicial!='')
		{
			$fecha_inicial2=date('Y-m-d',strtotime($fecha_inicial));
			$where_fecha=" and (tta.fecha='$fecha_inicial2')";
		}
		elseif($fecha_final!='')
		{
			$fecha_final2=date('Y-m-d',strtotime($fecha_final));
			$where_fecha=" and (tta.fecha='$fecha_final2')";
		}
		
		////////////////////FILTRO DE VEHICULOS///////////////////
		if($id_vehiculo>0)
		{
			$where_vehiculo=" and (v.id_vehiculo='$id_vehiculo' or v2.id_vehiculo='$id_vehiculo')";
			$select_vehiculo=" sum(tta.cantidad) as cantidad,";
			$group_by_vehiculo=" group by v.placa, tta.fecha";
		}
		else
		{
			$select_vehiculo=" tta.cantidad,";
		}
		
		//////////////////////FILTRO ARTICULOS/////////////////
		if($id_articulo!='' && $id_articulo!=0)/*Un artículo, todos vehículos*/ /*Un artículo, un vehículo*/
		{
			$query="SELECT tab.nombre, DATE_FORMAT(tta.fecha,'%d/%m/%Y') AS fecha_transaccion, tta.tipo_transaccion,
					COALESCE(IF((COALESCE(v.placa,'0')!='0'),v.placa,v2.placa),' ') AS placa, ".$select_vehiculo." tum.unidad_medida, COALESCE(tta.descripcion,'') AS descripcion,
					tab.id_articulo, tta.id_transaccion_articulo, v.id_vehiculo, tmi.id_mantenimiento_interno,
					tmr.id_mantenimiento_rutinario, tit.id_ingreso_taller
					FROM tcm_articulo_bodega AS tab
					INNER JOIN tcm_unidad_medida AS tum ON (tum.id_unidad_medida=tab.id_unidad_medida)
					INNER JOIN tcm_transaccion_articulo AS tta ON (tab.id_articulo=tta.id_articulo)
					LEFT JOIN tcm_mantenimiento_interno AS tmi ON (tta.id_mantenimiento_interno=tmi.id_mantenimiento_interno)
					LEFT JOIN tcm_mantenimiento_rutinario AS tmr ON (tmr.id_mantenimiento_rutinario=tta.id_mantenimiento_rutinario)
					LEFT JOIN tcm_ingreso_taller AS tit ON (tit.id_ingreso_taller=tmi.id_ingreso_taller)
					LEFT JOIN tcm_vehiculo AS v ON (tit.id_vehiculo=v.id_vehiculo)
					LEFT JOIN tcm_vehiculo AS v2 ON (v2.id_vehiculo=tmr.id_vehiculo)
					WHERE tab.id_articulo='$id_articulo' ".$where_fecha."  ".$where_vehiculo." ".$group_by_vehiculo."
					ORDER BY YEAR(tta.fecha) ASC, MONTH(tta.fecha) ASC, DAY(tta.fecha) ASC";
		}
		else
		{
			if($id_vehiculo!=0 && $id_vehiculo!="")/*Un vehículo, todos artículos*/
			{
				$query="SELECT tab.nombre, DATE_FORMAT(tta.fecha,'%d/%m/%Y') AS fecha_transaccion, tta.tipo_transaccion,
					COALESCE(IF((COALESCE(v.placa,'0')!='0'),v.placa,v2.placa),'Ingresos') AS placa, ".$select_vehiculo." tum.unidad_medida, COALESCE(tta.descripcion,'') AS descripcion,
					tab.id_articulo, tta.id_transaccion_articulo, v.id_vehiculo, tmi.id_mantenimiento_interno,
					tmr.id_mantenimiento_rutinario, tit.id_ingreso_taller
					FROM tcm_articulo_bodega AS tab
					INNER JOIN tcm_unidad_medida AS tum ON (tum.id_unidad_medida=tab.id_unidad_medida)
					INNER JOIN tcm_transaccion_articulo AS tta ON (tab.id_articulo=tta.id_articulo)
					LEFT JOIN tcm_mantenimiento_interno AS tmi ON (tmi.id_mantenimiento_interno=tta.id_mantenimiento_interno)
					LEFT JOIN tcm_mantenimiento_rutinario AS tmr ON (tmr.id_mantenimiento_rutinario=tta.id_mantenimiento_rutinario)
					LEFT JOIN tcm_ingreso_taller AS tit ON (tmi.id_ingreso_taller=tit.id_ingreso_taller)
					LEFT JOIN tcm_vehiculo AS v ON (tit.id_vehiculo=v.id_vehiculo)
					LEFT JOIN tcm_vehiculo AS v2 ON (v2.id_vehiculo=tmr.id_vehiculo)
					WHERE (tta.tipo_transaccion like 'salida') and (v.id_vehiculo='$id_vehiculo' or v2.id_vehiculo='$id_vehiculo') ".$where_fecha."
					GROUP BY tab.id_articulo";
			}
			else /*todos artículos, todos vehículos*/
			{
				$query="
						 SELECT tab.id_articulo, tab.nombre, (COALESCE(tab.cantidad, '0') + COALESCE(sal.salidas, '0') - COALESCE(ent.entradas, '0')) AS existencia_inicial,
						 COALESCE(ent.entradas, '0') AS entradas, COALESCE(sal.salidas, '0') AS salidas, COALESCE(tab.cantidad, '0') AS existencia_final,
						 tum.unidad_medida
						 FROM tcm_articulo_bodega AS tab
						 INNER JOIN tcm_unidad_medida AS tum ON (tum.id_unidad_medida = tab.id_unidad_medida)
						 LEFT JOIN tcm_transaccion_articulo AS tta ON (tab.id_articulo = tta.id_articulo)
						 LEFT JOIN
						 (
								SELECT SUM(tta.cantidad) AS entradas, tta.id_articulo
								FROM tcm_articulo_bodega AS tab
								LEFT JOIN tcm_transaccion_articulo AS tta ON (tab.id_articulo = tta.id_articulo)
								WHERE tta.tipo_transaccion LIKE 'entrada' ".$where_fecha."
								GROUP BY tta.id_articulo
						 ) AS ent ON (ent.id_articulo = tab.id_articulo)
						 LEFT JOIN
						 (
								SELECT SUM(tta.cantidad) AS salidas, tta.id_articulo
								FROM tcm_articulo_bodega AS tab
								LEFT JOIN tcm_transaccion_articulo AS tta ON (tab.id_articulo = tta.id_articulo)
								WHERE tta.tipo_transaccion LIKE 'salida' ".$where_fecha."
								GROUP BY tta.id_articulo
						 ) AS sal ON (sal.id_articulo = tab.id_articulo)
						 WHERE (COALESCE(ent.entradas, '0')>'0' OR COALESCE(sal.salidas, '0')>'0' OR tab.cantidad>0)  ".$where_fecha."
						 GROUP BY tab.id_articulo
				";
			}
		}
		
		$query=$this->db->query($query);
		return  $query->result_array();
	}
	/*******************************************************************************************************************************************/
	
	////////////////////////////////////////////////////FUNCIONES DE MANTENIMIENTO////////////////////////////////////////////////////////////////
	
	/**********************************FUNCIÓN PARA OBETENER LAS REVISIONES INTERNAS Y EXTERNAS***************************************************/
	function consultar_revisiones()
	{
		$query="SELECT * FROM tcm_revision WHERE estado=1";
		$query=$this->db->query($query);
		return  $query->result_array();
	}
	/*********************************************************************************************************************************************/
	
	/*************************FUNCIÓN PARA OBETENER LAS REVISIONES INTERNAS Y EXTERNAS DE ACUERDO A UN INGRESO A TALLER***********************/
	function consultar_revisiones2($id_ingreso_taller)
	{
		$query="SELECT tr.id_revision, tr.revision, tcr.varios, tr.tipo
				FROM tcm_chequeo_revision AS tcr
				INNER JOIN tcm_revision AS tr on(tcr.id_revision=tr.id_revision)
				WHERE tcr.id_ingreso_taller='$id_ingreso_taller'
				ORDER BY tcr.id_revision ASC";
		$query=$this->db->query($query);
		return  $query->result_array();
	}
	/*********************************************************************************************************************************************/
	
	/**********************************FUNCIÓN PARA OBETENER LAS REPARACIONES***************************************************/
	function consultar_reparaciones()
	{
		$query="SELECT * FROM tcm_reparacion WHERE estado=1";
		$query=$this->db->query($query);
		return  $query->result_array();
	}
	/*********************************************************************************************************************************************/
	
	/*************************FUNCIÓN PARA OBETENER LAS REPARACIONES DE ACUERDO A UN INGRESO A UN MANTENIMIENTOS***********************/
	function consultar_reparaciones2($id_mantenimiento_interno)
	{
		$query="SELECT tr.id_reparacion, tr.reparacion, tr.tipo
				FROM tcm_chequeo_reparacion AS tcr
				INNER JOIN tcm_reparacion AS tr on (tcr.id_reparacion=tr.id_reparacion)
				WHERE tcr.id_mantenimiento_interno='$id_mantenimiento_interno'
				ORDER BY tcr.id_reparacion ASC";
		$query=$this->db->query($query);
		return  $query->result_array();
	}
	/*********************************************************************************************************************************************/
	
	/*******************FUNCIÓN PARA OBTENER LOS VEHÍCULOS QUE HAN PASADO POR MANTENIMIENTO PREVENTIVO***************************************/
	function consultar_vehiculos_ingreso_taller($id_ingreso_taller=NULL)
	{
		$where="";
		if($id_ingreso_taller!=NULL) $where=" where tit.id_ingreso_taller='$id_ingreso_taller'";
			$query="SELECT tit.id_ingreso_taller, COALESCE(DATE_FORMAT(tit.fecha_entrega,'%d-%m-%Y'),'No ha sido dado de alta') as fecha_entrega,
					DATE_FORMAT(tit.fecha_recepcion,'%d-%m-%Y') as fecha_recepcion, tit.id_vehiculo,
					v.placa, vm.nombre as marca, vmo.modelo, vc.nombre_clase clase
					FROM tcm_ingreso_taller as tit
					INNER JOIN tcm_vehiculo as v on (tit.id_vehiculo=v.id_vehiculo)
					inner join tcm_vehiculo_marca as vm on (v.id_marca=vm.id_vehiculo_marca)
					inner join tcm_vehiculo_modelo as vmo on (v.id_modelo=vmo.id_vehiculo_modelo)
					inner join tcm_vehiculo_clase as vc on (v.id_clase=vc.id_vehiculo_clase)";
		$query=$this->db->query($query);
		return  $query->result_array();
	}
	/*********************************************************************************************************************************************/
	
	/**********************************FUNCIÓN PARA OBTENER LOS MANTENIMIENTOS A UN VEHÍCULO O INGRESO A TALLER INTERNO*****************************/
	function consultar_mantenimientos($id_vehiculo=NULL,$id_ingreso_taller=NULL)
	{
		$where="";
		if($id_vehiculo!=NULL)
		{
			if($id_ingreso_taller!=NULL) $where=" and tit.id_ingreso_taller='$id_ingreso_taller'";
			
			$query="SELECT  DATE_FORMAT(tmi.fecha,'%d-%m-%Y') AS fecha, tmi.observaciones, tmi.otro_mtto, 
					GROUP_CONCAT(tr.reparacion SEPARATOR '<br>') as reparacion,tit.id_vehiculo, tr.tipo
					FROM tcm_mantenimiento_interno AS tmi
					INNER JOIN tcm_ingreso_taller AS tit ON (tit.id_ingreso_taller=tmi.id_ingreso_taller)
					LEFT JOIN tcm_chequeo_reparacion AS tcr ON (tmi.id_mantenimiento_interno=tcr.id_mantenimiento_interno)
					LEFT JOIN tcm_reparacion AS tr ON (tcr.id_reparacion=tr.id_reparacion)
					WHERE tit.id_vehiculo='$id_vehiculo' ".$where."
					GROUP BY fecha, tipo, tmi.id_mantenimiento_interno
					ORDER BY YEAR(fecha) DESC, MONTH(fecha) DESC, DAY(fecha) DESC";
			
			$query=$this->db->query($query);
			return  $query->result_array();
		}
		elseif($id_ingreso_taller!=NULL)
		{
			$query="SELECT  DATE_FORMAT(tmi.fecha,'%d-%m-%Y') AS fecha, tmi.observaciones, tmi.otro_mtto, 
					GROUP_CONCAT(tr.reparacion SEPARATOR '<br>') as reparacion,tit.id_vehiculo, tr.tipo
					FROM tcm_mantenimiento_interno AS tmi
					INNER JOIN tcm_ingreso_taller AS tit ON (tit.id_ingreso_taller=tmi.id_ingreso_taller)
					LEFT JOIN tcm_chequeo_reparacion AS tcr ON (tmi.id_mantenimiento_interno=tcr.id_mantenimiento_interno)
					LEFT JOIN tcm_reparacion AS tr ON (tcr.id_reparacion=tr.id_reparacion)
					WHERE tit.id_ingreso_taller='$id_ingreso_taller'
					GROUP BY fecha, tipo, tmi.id_mantenimiento_interno
					ORDER BY YEAR(fecha) DESC, MONTH(fecha) DESC, DAY(fecha) DESC";
			
			$query=$this->db->query($query);
			return  $query->result_array();
		}
		else return 0;
	}
	/*********************************************************************************************************************************************/
	
	/**********************************FUNCIÓN PARA OBTENER LOS MANTENIMIENTOS DE UN VEHÍCULO***************************************************/
	function consultar_mantenimientos2($id_mantenimiento_interno=NULL)
	{
		$where=" ";
		if($id_mantenimiento_interno!=NULL) $where=" where tmi.id_mantenimiento_interno='$id_mantenimiento_interno'";
			$query="select v.placa, IF(vmot.id_empleado=0,'No tiene asignado',LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido,s.apellido_casada))) AS motorista,
					o.nombre_seccion as seccion, vm.nombre as marca, vmo.modelo, vc.nombre_clase clase, vcon.condicion, COALESCE(max(vk.km_final),'0') as kilometraje, v.anio, v.estado,
					ff.nombre_fuente_fondo as fuente_fondo,v.imagen, v.id_seccion, v.id_clase, v.id_condicion, v.id_fuente_fondo, v.id_marca, v.id_modelo, v.id_vehiculo, vmot.id_empleado,
					v.tipo_combustible, DATE_FORMAT(tmi.fecha,'%d-%m-%Y') as fecha, tmi.id_ingreso_taller, tmi.id_mantenimiento_interno, tmi.otro_mtto, tmi.observaciones,
					temp.nombre as mecanico, it.kilometraje_ingreso
					from tcm_vehiculo as v
					inner join tcm_vehiculo_marca as vm on (v.id_marca=vm.id_vehiculo_marca)
					inner join tcm_vehiculo_modelo as vmo on (v.id_modelo=vmo.id_vehiculo_modelo)
					inner join tcm_vehiculo_clase as vc on (v.id_clase=vc.id_vehiculo_clase)
					inner join tcm_vehiculo_condicion as vcon on (v.id_condicion=vcon.id_vehiculo_condicion)
					left join tcm_vehiculo_motorista as vmot on (v.id_vehiculo=vmot.id_vehiculo)
					left join tcm_vehiculo_kilometraje as vk on (vk.id_vehiculo=v.id_vehiculo)
					left join sir_empleado as s on (vmot.id_empleado=s.id_empleado)
					inner join org_seccion as o on (v.id_seccion=o.id_seccion)
					inner join tcm_fuente_fondo as ff on (ff.id_fuente_fondo=v.id_fuente_fondo)
					inner join tcm_ingreso_taller as it on (it.id_vehiculo=v.id_vehiculo)
					INNER JOIN tcm_mantenimiento_interno as tmi on (tmi.id_ingreso_taller=it.id_ingreso_taller)
					inner join tcm_empleado as temp on (tmi.id_empleado_mtto=temp.id_empleado)
					".$where."
					GROUP BY tmi.id_mantenimiento_interno
					ORDER BY tmi.id_mantenimiento_interno DESC";
		$query=$this->db->query($query);
		return  $query->result_array();
	}
	/*********************************************************************************************************************************************/
	
	/**********************************FUNCIÓN PARA VER LOS VEHÍCULOS EN TALLER INTERNO***************************************************/
	function vehiculos_taller_interno($id=0, $estado=NULL, $id_ingreso_taller=NULL,$rutinario=NULL)
	{
		$where="";
		
		if($rutinario==NULL) $join="inner";
		else $join="left";
		
		if($id!=0 && $estado!=NULL) $where=" where (v.id_vehiculo='$id' and v.estado='$estado')";
		elseif($id!=0)	$where=" where v.id_vehiculo='$id'";
		elseif($estado!=NULL)	$where=" where v.estado='$estado' AND (IF(COALESCE(it.fecha_entrega,'false')='false',TRUE,FALSE))";
		elseif($id_ingreso_taller!=NULL) $where=" WHERE it.id_ingreso_taller='$id_ingreso_taller'";
		
		$query="select v.placa, IF(vmot.id_empleado=0,'No tiene asignado',LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido,s.apellido_casada))) AS motorista,
				o.nombre_seccion as seccion, vm.nombre as marca, vmo.modelo, vc.nombre_clase clase, vcon.condicion, COALESCE(max(vk.km_final),'0') as kilometraje, v.anio, v.estado,
				ff.nombre_fuente_fondo as fuente_fondo,v.imagen, v.id_seccion, v.id_clase, v.id_condicion, v.id_fuente_fondo, v.id_marca, v.id_modelo, v.id_vehiculo, vmot.id_empleado,
				v.tipo_combustible, it.id_ingreso_taller, DATE_FORMAT(it.fecha_recepcion,'%d-%m-%Y') as fecha_recepcion, DATE_FORMAT(it.fecha_entrega,'%d-%m-%Y') as fecha_entrega, it.trabajo_solicitado,
				it.trabajo_solicitado_carroceria, it.notas, it.kilometraje_ingreso, emp1.id_empleado AS id_persona_recibe, emp1.nombre as persona_recibe, emp2.id_empleado as id_persona_entrega, emp2.nombre as persona_entrega
				from tcm_vehiculo as v
				inner join tcm_vehiculo_marca as vm on (v.id_marca=vm.id_vehiculo_marca)
				inner join tcm_vehiculo_modelo as vmo on (v.id_modelo=vmo.id_vehiculo_modelo)
				inner join tcm_vehiculo_clase as vc on (v.id_clase=vc.id_vehiculo_clase)
				inner join tcm_vehiculo_condicion as vcon on (v.id_condicion=vcon.id_vehiculo_condicion)
				left join tcm_vehiculo_motorista as vmot on (v.id_vehiculo=vmot.id_vehiculo)
				left join tcm_vehiculo_kilometraje as vk on (vk.id_vehiculo=v.id_vehiculo)
				left join sir_empleado as s on (vmot.id_empleado=s.id_empleado)
				inner join org_seccion as o on (v.id_seccion=o.id_seccion)
				inner join tcm_fuente_fondo as ff on (ff.id_fuente_fondo=v.id_fuente_fondo)
				".$join." join tcm_ingreso_taller as it on (it.id_vehiculo=v.id_vehiculo)
				left join(select e.id_empleado, e.nombre from tcm_empleado as e) as emp1 on (it.id_motorista_recibe=emp1.id_empleado)
				left join(select e.id_empleado, e.nombre from tcm_empleado as e) as emp2 on (it.id_usuario_recibe_taller=emp2.id_empleado)
				".$where."
				GROUP BY it.id_ingreso_taller";
		$query=$this->db->query($query);
		return  $query->result_array();
	}
	/*********************************************************************************************************************************************/
	
	/**********************************FUNCIÓN PARA GUARDAR EL INGRESO DEL VEHICULO A TALLER INTERNO***********************************************/
	
	function guardar_mtto($datos)
	{
		extract($datos);
		$fecha_recepcion2=date('Y-m-d',strtotime($fecha_recepcion));
		$consulta="INSERT INTO tcm_ingreso_taller (fecha_recepcion, id_vehiculo, trabajo_solicitado, trabajo_solicitado_carroceria, id_usuario_recibe_taller, kilometraje_ingreso) VALUES ('$fecha_recepcion2', '$id_vehiculo', '$trabajo_solicitado', '$trabajo_solicitado_carroceria', '$id_usuario_recibe_taller', '$kilometraje_ingreso');";
		if($this->db->query($consulta))
		{
			$bandera=1;
			$revision=array_merge($revision1, $revision2);
			if(!empty($revision))
			{
				$id_ingreso_taller=$this->ultimo_id_ingreso_taller();
				$n=count($revision);
				$y=0;				
				for($i=0;$i<$n;$i++)
				{
					$id_revision=$revision[$i];
					if($this->revision_varios($id_revision)==1)/*Evalúa si el accesorio tiene una cantidad*/
					{
						$varios=$cantidad[$y];
						$y++;
					}
					else $varios=0;
					
					$query="INSERT INTO tcm_chequeo_revision (id_revision, id_ingreso_taller, varios) VALUES ('$id_revision', '$id_ingreso_taller', '$varios');";
					if($this->db->query($query)) $bandera=$bandera*1;
					else $bandera=$bandera*0;
				}
			}
			if($bandera==1)
			{
				$consulta2="UPDATE tcm_vehiculo SET estado='2' WHERE (id_vehiculo='$id_vehiculo');";
				if($this->db->query($consulta2))
				{
					$consulta3="INSERT INTO tcm_vehiculo_kilometraje(id_vehiculo,km_final) values('$id_vehiculo','$kilometraje_ingreso');";
					return $this->db->query($consulta3);
				}
			}
		}
	}
	/****************************************************************************************************************************************/
	
	/**********************************FUNCIÓN PARA MODIFICAR EL INGRESO DEL VEHICULO A TALLER INTERNO***********************************************/
	
	function modificar_mtto($datos)
	{
		extract($datos);
		$fecha_recepcion2=date('Y-m-d',strtotime($fecha_recepcion));
		$consulta="UPDATE tcm_ingreso_taller SET id_vehiculo='$id_vehiculo', trabajo_solicitado='$trabajo_solicitado', fecha_recepcion='$fecha_recepcion2',
							trabajo_solicitado_carroceria='$trabajo_solicitado_carroceria', kilometraje_ingreso='$kilometraje_ingreso',
							id_usuario_recibe_taller='$id_usuario_recibe_taller' WHERE  (id_ingreso_taller='$id_ingreso_taller');";
		if($this->db->query($consulta))
		{
			$bandera=1;
			$query2="delete from tcm_chequeo_revision where id_ingreso_taller='$id_ingreso_taller'";
			if($this->db->query($query2)) $bandera=$bandera*1;
			else $bandera=$bandera*0;
			
			$revision=array_merge($revision1, $revision2);
			if(!empty($revision))
			{
				$n=count($revision);
				$y=0;				
				for($i=0;$i<$n;$i++)
				{
					$id_revision=$revision[$i];
					if($this->revision_varios($id_revision)==1)/*Evalúa si el accesorio tiene una cantidad*/
					{
						$varios=$cantidad[$y];
						$y++;
					}
					else $varios=0;
					
					$query="INSERT INTO tcm_chequeo_revision (id_revision, id_ingreso_taller, varios) VALUES ('$id_revision', '$id_ingreso_taller', '$varios');";
					if($this->db->query($query)) $bandera=$bandera*1;
					else $bandera=$bandera*0;
				}
			}
			if($bandera==1)
			{
				$id_mision=$this->transporte_model->get_last_id_mision($id_vehiculo_original);
				
				
				if($id_vehiculo==$id_vehiculo_original)
				{
					$consulta3="update tcm_vehiculo_kilometraje set km_final = '$kilometraje_ingreso' where id_mision='$id_mision'";
					return $this->db->query($consulta3);					
				}				
				else
				{
					$consulta3="update tcm_vehiculo_kilometraje set km_final = '$kilometraje_ingreso', id_vehiculo='$id_vehiculo' where id_mision='$id_mision'";
					if($this->db->query($consulta3))
					{
						$consulta4="UPDATE tcm_vehiculo SET estado='2' WHERE (id_vehiculo='$id_vehiculo');";
						if($this->db->query($consulta4))
						{
							$consulta5="UPDATE tcm_vehiculo SET estado='1' WHERE (id_vehiculo='$id_vehiculo_original');";
							return $this->db->query($consulta5);
						}
					}
				}
			}
		}
	}
	/*********************************************************************************************************************************************/
	
	function get_last_id_mision($id)
	{
		$consulta="select max(id_mision) as id_mision from tcm_vehiculo_kilometraje where (id_vehiculo='$id_vehiculo_original' and combustibleIni=0)";
		$consulta=$this->db->query($consulta);
		$id_mis=$consulta->result_array();
		foreach($id_mis as $id_m)
		{
			return $id_m['id_mision'];
		}
	}
	
	/*******************************************FUNCIÓN PARA OBTENER EL ÚLTIMO ID_INGRESO_TALLER********************************************/
	function ultimo_id_ingreso_taller()
	{
		$query="select max(id_ingreso_taller) as id_ingreso_taller from tcm_ingreso_taller;";
		$query=$this->db->query($query);
		$id=$query->result_array();
		foreach($id as $i)
		{
			return $i['id_ingreso_taller'];
		}
	}
	/*******************************************************************************************************************************************/
	
	/*******************************************FUNCIÓN PARA OBTENER EL ÚLTIMO ID_INGRESO_TALLER********************************************/
	function revision_varios($id)
	{
		$query="select cantidad from tcm_revision where id_revision='$id';";
		$query=$this->db->query($query);
		$cant=$query->result_array();
		foreach($cant as $c)
		{
			return $c['cantidad'];
		}
	}
	/*******************************************************************************************************************************************/
	
	/**********************************FUNCIÓN PARA OBETENER LAS REPARACIONES DE MANTENIMIENTO E INSPECCIÓN/CHEQUEO*********************************/
	function consultar_reparacion()
	{
		$query="SELECT * FROM tcm_reparacion WHERE estado=1";
		$query=$this->db->query($query);
		return  $query->result_array();
	}
	/*********************************************************************************************************************************************/
	
	/*******************************FUNCIÓN PARA GUARDAR LOS MANTENIMIENTOS DE VEHÍCULOS EN TALLER INTERNO*****************************************/
	function guardar_taller($datos)
	{
		extract($datos);
		$fecha2=date('Y-m-d',strtotime($fecha));
		$consulta="INSERT INTO tcm_mantenimiento_interno (id_ingreso_taller, otro_mtto, observaciones, fecha, id_empleado_mtto)
					VALUES ('$id_ingreso_taller','$otro_mtto', '$observaciones', '$fecha2', '$id_empleado_mtto');";
		if($this->db->query($consulta))
		{
			$fecha3=date('Y-m-d');
			$bandera=1;
			$id_mantenimiento_interno=$this->ultimo_id_mantenimiento_interno();
			$reparacion=array_merge($reparacion1, $reparacion2);
			if(!empty($reparacion))
			{
				$n=count($reparacion);
				
				for($i=0;$i<$n;$i++)
				{
					$id_reparacion=$reparacion[$i];
					$query="INSERT INTO tcm_chequeo_reparacion (id_reparacion, id_mantenimiento_interno) VALUES ('$id_reparacion', '$id_mantenimiento_interno');";
					if($this->db->query($query)) $bandera=$bandera*1;
					else $bandera=$bandera*0;
				}
			}
			if($bandera==1)
			{
				$bandera2=1;
				if(!empty($id_articulo))
				{
					for($j=0;$j<count($id_articulo);$j++)
					{
						$id_art=$id_articulo[$j];
						$cantidad_utilizada=$cant_usada[$j];
						
						$inventario=$this->inventario($id_art);
						foreach($inventario as $inv)
						{
							$cantidad_disponible=$inv['cantidad'];
							$unidad_medida=$inv['unidad_medida'];
							$articulo=$inv['nombre'];
						}
						
						if($cantidad_utilizada==1)	$descripcion="Se usó ".$cantidad_utilizada." ".$unidad_medida." de ".$articulo;
						else $descripcion="Se usaron ".$cantidad_utilizada." ".$unidad_medida." de ".$articulo;
						
						$cantidad_final=$cantidad_disponible-$cantidad_utilizada;
						
						$query2="UPDATE tcm_articulo_bodega SET cantidad='$cantidad_final' WHERE (id_articulo='$id_art');";
						if($this->db->query($query2))
						{
							$presupuesto=$this->presupuesto_activo();
							foreach($presupuesto as $p)
							{
								$id_presupuesto=$p['id_presupuesto'];
							}
							
							$query3="INSERT INTO tcm_transaccion_articulo (tipo_transaccion, cantidad, fecha, id_articulo, id_mantenimiento_interno, descripcion, id_presupuesto) VALUES ('SALIDA', '$cantidad_utilizada', '$fecha3', '$id_art', '$id_mantenimiento_interno', '$descripcion', '$id_presupuesto');";
							if($this->db->query($query3)) $bandera2=$bandera2*1;
							else $bandera2=$bandera2*0;
						}
					}
					if($bandera2==1) return true;
					else return false;
				}
				else return true;
			}
			else return false;
		}
		else return false;
	}
	/*********************************************************************************************************************************************/
	
	/*******************************FUNCIÓN PARA GUARDAR LOS MANTENIMIENTOS RUTINARIOS DE LOS VEHÍCULOS*****************************************/
	function guardar_mtto_rutinario($datos)
	{
		extract($datos);
		
		$fecha2=date('Y-m-d',strtotime($fecha));
		$consulta="INSERT INTO tcm_mantenimiento_rutinario (id_vehiculo, trabajo_realizado, id_empleado_repara, fecha) VALUES ('$id_vehiculo', '$trabajo_realizado', '$id_empleado_repara', '$fecha2');";
		if($this->db->query($consulta))
		{
			$bandera2=1;
			$id_mantenimiento_rutinario=$this->ultimo_id_mantenimiento_rutinario();
			if(!empty($id_articulo))
			{
				for($j=0;$j<count($id_articulo);$j++)
				{
					$id_art=$id_articulo[$j];
					
					$cantidad_utilizada=$cant_usada[$j];
					
					$inventario=$this->inventario($id_art);
					foreach($inventario as $inv)
					{
						$cantidad_disponible=$inv['cantidad'];
						$unidad_medida=$inv['unidad_medida'];
						$articulo=$inv['nombre'];
					}
					
					$cantidad_final=$cantidad_disponible-$cantidad_utilizada;
					
					if($cantidad_utilizada==1)	$descripcion="Se usó ".$cantidad_utilizada." ".$unidad_medida." de ".$articulo;
					else $descripcion="Se usaron ".$cantidad_utilizada." ".$unidad_medida." de ".$articulo;
					
					$query2="UPDATE tcm_articulo_bodega SET cantidad='$cantidad_final' WHERE (id_articulo='$id_art');";
					if($this->db->query($query2))
					{
						$presupuesto=$this->presupuesto_activo();
						foreach($presupuesto as $p)
						{
							$id_presupuesto=$p['id_presupuesto'];
						}
						
						$query3="INSERT INTO tcm_transaccion_articulo (tipo_transaccion, cantidad, fecha, id_articulo, id_mantenimiento_rutinario, descripcion, id_presupuesto) VALUES ('SALIDA', '$cantidad_utilizada', '$fecha2', '$id_art', '$id_mantenimiento_rutinario', '$descripcion', '$id_presupuesto');";
						if($this->db->query($query3)) $bandera2=$bandera2*1;
						else $bandera2=$bandera2*0;
					}
				}
				if($bandera2==1) return true;
				else return false;
			}
			else return true;
		}
	}
	/*********************************************************************************************************************************************/
	
	/*******************************FUNCIÓN PARA GUARDAR LOS MANTENIMIENTOS DE VEHÍCULOS EN TALLER EXTERNO*****************************************/
	function alta_taller_MTPS($datos)
	{
		extract($datos);
		$fecha_entrega2=date('Y-m-d',strtotime($fecha_entrega));
				
		$consulta="UPDATE tcm_ingreso_taller SET fecha_entrega='$fecha_entrega2', id_motorista_recibe='$id_motorista_recibe', notas='$notas' WHERE id_ingreso_taller='$id_ingreso_taller';";
		if($this->db->query($consulta))
		{
			$consulta2="UPDATE tcm_vehiculo SET estado='1' WHERE (id_vehiculo='$id_vehiculo');";
			return $this->db->query($consulta2);
		}
	}
	/*********************************************************************************************************************************************/
	
	/*******************************************FUNCIÓN PARA OBTENER EL ÚLTIMO ID_MANTENIMIENTO_INTERNO********************************************/
	function ultimo_id_mantenimiento_interno()
	{
		$query="select max(id_mantenimiento_interno) as id_mantenimiento_interno from tcm_mantenimiento_interno;";
		$query=$this->db->query($query);
		$id=$query->result_array();
		foreach($id as $i)
		{
			return $i['id_mantenimiento_interno'];
		}
	}
	/*******************************************************************************************************************************************/
	
	/*******************************************FUNCIÓN PARA OBTENER EL ÚLTIMO ID_MANTENIMIENTO_RUTINARIO********************************************/
	function ultimo_id_mantenimiento_rutinario()
	{
		$query="select max(id_mantenimiento_rutinario) as id_mantenimiento_rutinario from tcm_mantenimiento_rutinario;";
		$query=$this->db->query($query);
		$id=$query->result_array();
		foreach($id as $i)
		{
			return $i['id_mantenimiento_rutinario'];
		}
	}
	/*******************************************************************************************************************************************/
	
	/************************************FUNCIÓN PARA OBTENER LOS VEHÍCULOS QUE SE ENCUENTRAN EN TALLER EXTERNO*********************************/
	function vehiculos_taller_externo($id_vehiculo=0,$estado=NULL,$id_ingreso_taller_ext=NULL)
	{
		$where="";
		if($id_vehiculo!=0 && $estado!=NULL) $where=" where (v.id_vehiculo='$id_vehiculo' and v.estado='$estado')";
		elseif($id_vehiculo!=0)	$where=" where v.id_vehiculo='$id_vehiculo'";
		elseif($estado!=NULL)	$where=" where v.estado='$estado' AND (IF(COALESCE(itx.fecha_entrega,'false')='false',TRUE,FALSE))";
		elseif($id_ingreso_taller_ext!=NULL) $where=" WHERE itx.id_ingreso_taller_ext='$id_ingreso_taller_ext'";
		
		$query="select v.placa, IF(vmot.id_empleado=0,'No tiene asignado',LOWER(CONCAT_WS(' ',s.primer_nombre, s.segundo_nombre, s.tercer_nombre, s.primer_apellido,s.segundo_apellido,s.apellido_casada))) AS motorista,
				o.nombre_seccion as seccion, vm.nombre as marca, vmo.modelo, vc.nombre_clase clase, vcon.condicion, COALESCE(max(vk.km_final),'0') as kilometraje, v.anio, v.estado,
				ff.nombre_fuente_fondo as fuente_fondo,v.imagen, v.id_seccion, v.id_clase, v.id_condicion, v.id_fuente_fondo, v.id_marca, v.id_modelo, v.id_vehiculo, vmot.id_empleado, te.id_taller_externo,
				v.tipo_combustible, itx.id_ingreso_taller_ext, itx.id_ingreso_taller, DATE_FORMAT(itx.fecha_recepcion,'%d-%m-%Y') as fecha_recepcion, itx.trabajo_solicitado, te.nombre as taller
				from tcm_vehiculo as v
				inner join tcm_vehiculo_marca as vm on (v.id_marca=vm.id_vehiculo_marca)
				inner join tcm_vehiculo_modelo as vmo on (v.id_modelo=vmo.id_vehiculo_modelo)
				inner join tcm_vehiculo_clase as vc on (v.id_clase=vc.id_vehiculo_clase)
				inner join tcm_vehiculo_condicion as vcon on (v.id_condicion=vcon.id_vehiculo_condicion)
				left join tcm_vehiculo_motorista as vmot on (v.id_vehiculo=vmot.id_vehiculo)
				left join tcm_vehiculo_kilometraje as vk on (vk.id_vehiculo=v.id_vehiculo)
				left join sir_empleado as s on (vmot.id_empleado=s.id_empleado)
				inner join org_seccion as o on (v.id_seccion=o.id_seccion)
				inner join tcm_fuente_fondo as ff on (ff.id_fuente_fondo=v.id_fuente_fondo)
				inner join tcm_ingreso_taller as it on (it.id_vehiculo=v.id_vehiculo)
				inner join tcm_ingreso_taller_ext as itx on (itx.id_ingreso_taller=it.id_ingreso_taller)
				inner join tcm_taller_externo as te on (te.id_taller_externo=itx.id_taller_externo) ".$where."
				GROUP BY itx.id_ingreso_taller_ext";
		$query=$this->db->query($query);
		return  $query->result_array();
	}
	/*******************************************************************************************************************************************/
	
	/************************************FUNCIÓN PARA OBTENER LA INFORMACIÓN DE REGISTROS EN TALLER EXTERNO*********************************/
	function consultar_ingresos_taller_externo($id_ingreso_taller=NULL)
	{
		$where="";
		if($id_ingreso_taller!=NULL) $where=" WHERE tit.id_ingreso_taller='$id_ingreso_taller'";
		
		$query="SELECT itx.id_ingreso_taller_ext, itx.id_ingreso_taller, DATE_FORMAT(itx.fecha_recepcion,'%d-%m-%Y') AS fecha_recepcion,
							 DATE_FORMAT(itx.fecha_entrega,'%d-%m-%Y') AS fecha_entrega, itx.trabajo_realizado, itx.trabajo_solicitado,
							 itx.id_gasto, itx.id_taller_externo, itx.id_usuario, tit.id_vehiculo
				FROM tcm_ingreso_taller_ext AS itx
				INNER JOIN tcm_taller_externo AS tte ON (tte.id_taller_externo=itx.id_taller_externo)
				INNER JOIN tcm_ingreso_taller AS tit ON (tit.id_ingreso_taller=itx.id_ingreso_taller) ".$where."
				ORDER BY YEAR(itx.fecha_entrega) DESC, MONTH(itx.fecha_entrega) DESC, DAY(itx.fecha_entrega) DESC";
		$query=$this->db->query($query);
		return  $query->result_array();
	}
	/*******************************************************************************************************************************************/
	
	/*******************************FUNCIÓN PARA GUARDAR LOS MANTENIMIENTOS DE VEHÍCULOS EN TALLER EXTERNO*****************************************/
	function guardar_taller_ext($datos)
	{
		extract($datos);
		$fecha_recepcion2=date('Y-m-d',strtotime($fecha_recepcion));
		$bandera=1;
		if($ntaller_ext!=NULL && $ntaller_ext!="")
		{
			$preconsulta="INSERT INTO tcm_taller_externo(nombre,estado) VALUES ('$ntaller_ext',1)";
			if($this->db->query($preconsulta))
			{
				$id_taller_externo=$this->get_last_id_taller_externo();
				$bandera=1;
			}
			else $bandera=0;
			
		}
		
		if($bandera==1)
		{
			$consulta="INSERT INTO tcm_ingreso_taller_ext (trabajo_solicitado, fecha_recepcion, id_usuario, id_taller_externo, id_ingreso_taller)
									VALUES ('$trabajo_solicitado', '$fecha_recepcion2', '$id_usuario', '$id_taller_externo', '$id_ingreso_taller');";
			if($this->db->query($consulta))
			{
				$consulta2="UPDATE tcm_vehiculo SET estado='3' WHERE (id_vehiculo='$id_vehiculo');";
				return $this->db->query($consulta2);
			}
		}
		else return false;
	}
	/*********************************************************************************************************************************************/
	
	
		/*******************************FUNCIÓN PARA MODIFICAR LOS MANTENIMIENTOS DE VEHÍCULOS EN TALLER EXTERNO*****************************************/
	function modificar_taller_ext($datos)
	{
		extract($datos);
		$fecha_recepcion2=date('Y-m-d',strtotime($fecha_recepcion));
		$bandera=1;
		if($ntaller_ext!=NULL && $ntaller_ext!="")
		{
			$preconsulta="INSERT INTO tcm_taller_externo(nombre,estado) VALUES ('$ntaller_ext',1)";
			if($this->db->query($preconsulta))
			{
				$id_taller_externo=$this->get_last_id_taller_externo();
				$bandera=1;
			}
			else $bandera=0;
			
		}
		
		if($bandera==1)
		{			
			$update="";
			if($id_ingreso_taller!='NULO')
			{
				$update=", id_ingreso_taller='$id_ingreso_taller'";
			}
			$consulta="UPDATE tcm_ingreso_taller_ext SET trabajo_solicitado='$trabajo_solicitado', fecha_recepcion='$fecha_recepcion2',
						id_taller_externo='$id_taller_externo' ".$update."	WHERE id_ingreso_taller_ext='$id_ingreso_taller_ext'";
			if($this->db->query($consulta))
			{
				if($id_vehiculo_original!=$id_vehiculo)
				{
					$estado=$this->get_vehiculo_estado($id_vehiculo);///estado del nuevo vehiculo
					
					$consulta2="UPDATE tcm_vehiculo SET estado='3' WHERE (id_vehiculo='$id_vehiculo');";////estado 3 al vehiculo nuevo
					if($this->db->query($consulta2))
					{
						$consulta3="UPDATE tcm_vehiculo SET estado='$estado' WHERE (id_vehiculo='$id_vehiculo_original');";///estado del vehiculo nuevo al vehiculo viejo
						if($this->db->query($consulta3))
						{
							if($id_ingreso_taller=='NULO')
							{
								////La información de ingreso del taller pasa a ser la info del vehiculo nuevo
								$consulta4="UPDATE tcm_ingreso_taller SET id_vehiculo='$id_vehiculo' WHERE (id_ingreso_taller='$id_ingreso_taller_original');";
								return $this->db->query($consulta4);
							}
							else return true;
						}
						else return false;
					}
					else return false;
				}
				else return true;
			}
			else return false;
		}
		else return false;
	}
	/*********************************************************************************************************************************************/
	
	/*******************************************FUNCIÓN PARA OBTENER EL ÚLTIMO ID_TALLER_EXTERNO*************************************************/
	function get_last_id_taller_externo()
	{
		$query="select max(id_taller_externo) as id_taller_externo from tcm_taller_externo;";
		$query=$this->db->query($query);
		$id=$query->result_array();
		foreach($id as $i)
		{
			return $i['id_taller_externo'];
		}
	}
	/*******************************************************************************************************************************************/
	
	/*******************************************FUNCIÓN PARA OBTENER EL ESTADO DEL VEHICULO*************************************************/
	function get_vehiculo_estado($id_v)
	{
		$query="select estado from tcm_vehiculo where id_vehiculo='$id_v';";
		$query=$this->db->query($query);
		$estado=$query->result_array();
		foreach($estado as $est)
		{
			return $est['estado'];
		}
	}
	/*******************************************************************************************************************************************/
	
	/*******************************FUNCIÓN PARA GUARDAR LOS MANTENIMIENTOS DE VEHÍCULOS EN TALLER EXTERNO*****************************************/
	function alta_taller_ext($datos)
	{
		extract($datos);
		$bandera=1;
		$fecha_entrega2=date('Y-m-d',strtotime($fecha_entrega));
				
		if($adquisicion=='pagada' && $gasto>0.00)
		{
			$presupuesto=$this->presupuesto_activo();
			foreach($presupuesto as $p)
			{
				$id_presupuesto=$p['id_presupuesto'];
				$pre=$p['presupuesto'];
			}
			
			
			$gastos_a=$this->gastos($id_presupuesto);
			$gasto_s=0;
			foreach($gastos_a as $gastos_b)
			{
				$gasto_s=$gasto_s+$gastos_b['gasto'];
			}
			
			$cantidad_actual=$pre-$gasto_s;
			if($gasto<$cantidad_actual)/*se valida que no se gaste más de lo que se dispone*/
			{
				$fecha=date('Y-m-d');
				$descripcion2="Se canceló al taller externo la reparación del vehículo con número de placa: ".$placa;
				$query_gasto="INSERT INTO tcm_gasto_presupuesto (gasto, descripcion, fecha, id_presupuesto) VALUES ('$gasto', '$descripcion2', '$fecha', '$id_presupuesto');";
				if($this->db->query($query_gasto)) $bandera=$bandera*1;
				else $bandera=$bandera*0;
			}
		}
		
		if($bandera==1)
		{
			$id_gasto=$this->ultimo_id_gasto();
			$consulta="UPDATE tcm_ingreso_taller_ext SET trabajo_realizado='$trabajo_realizado', fecha_entrega='$fecha_entrega2', id_usuario='$id_usuario', id_gasto='$id_gasto' WHERE id_ingreso_taller_ext='$id_ingreso_taller_ext'";
			if($this->db->query($consulta))
			{
				$consulta2="UPDATE tcm_vehiculo SET estado='2' WHERE (id_vehiculo='$id_vehiculo');";
				return $this->db->query($consulta2);
			}
		}
	}
	/*********************************************************************************************************************************************/
	
	/*******************************FUNCIÓN PARA OBTENER LOS MECÁNICOS DEL MTPS*****************************************/
	function mecanicos()
	{
		$query="SELECT e.id_empleado, e.nombre, e.nominal, e.funcional, scf.id_cargo_funcional, scn.id_cargo_nominal
				FROM tcm_empleado AS e
				INNER JOIN sir_cargo_funcional AS scf ON (scf.funcional=e.funcional)
				INNER JOIN sir_cargo_nominal AS scn ON (scn.cargo_nominal=e.nominal)
				WHERE (scf.id_cargo_funcional=248 OR scf.id_cargo_funcional=289) OR (scn.id_cargo_nominal=99)";
		$query=$this->db->query($query);
		return  $query->result_array();
	}
	/*********************************************************************************************************************************************/
	
	/*******************************FUNCIÓN PARA OBTENER LOS REPORTES DE MANTENIMIENTOS*****************************************/
	function mantenimientos($info)
	{
		extract($info);
		$where_fecha="";
		//////////////////FILTRO DE FECHAS/////////////////////
		if($fecha_inicial!='' && $fecha_final!='')
		{
			$fecha_inicial2=date('Y-m-d',strtotime($fecha_inicial));
			$fecha_final2=date('Y-m-d',strtotime($fecha_final));
			$where_fecha=" where (fecha between '$fecha_inicial2' and '$fecha_final2')";
		}
		/*elseif($fecha_inicial=='' && $fecha_final=='')
		{
			$fecha_inicial=date('Y')."-01-01";
			$fecha_final=date('Y')."-12-31";
			$where_fecha="where (fecha between '$fecha_inicial' and '$fecha_final')";
		}*/
		elseif($fecha_inicial!='')
		{
			$fecha_inicial2=date('Y-m-d',strtotime($fecha_inicial));
			$where_fecha=" where (fecha='$fecha_inicial2')";
		}
		elseif($fecha_final!='')
		{
			$fecha_final2=date('Y-m-d',strtotime($fecha_final));
			$where_fecha=" where (fecha='$fecha_final2')";
		}
		
		
		if($mecanico!='' && $mecanico!=0)
		{
			if($id_vehiculo!='' && $id_vehiculo!=0)/*un mecanico un vehiculo*/
			{
				if($where_fecha!="") $where_fecha=$where_fecha." AND";
				else $where_fecha="WHERE";
				
				$query="SELECT e.id_empleado, e.nombre, (COALESCE(mtto_interno.interno,'0') + COALESCE(mtto_rutinario.rutinario,'0')) AS mttos, v.placa
						FROM tcm_empleado AS e
						LEFT JOIN
						(
							SELECT e.id_empleado, COUNT(v.id_vehiculo) AS interno, v.id_vehiculo
							FROM tcm_empleado AS e
							INNER JOIN tcm_mantenimiento_interno AS tmi ON (tmi.id_empleado_mtto=e.id_empleado)
							INNER JOIN tcm_ingreso_taller AS tit ON (tit.id_ingreso_taller=tmi.id_ingreso_taller)
							INNER JOIN tcm_vehiculo AS v ON (v.id_vehiculo=tit.id_vehiculo)
							".$where_fecha." e.id_empleado='".$mecanico."' AND v.id_vehiculo='".$id_vehiculo."'
							GROUP BY v.id_vehiculo
						) AS mtto_interno ON (mtto_interno.id_empleado=e.id_empleado)
						LEFT JOIN
						(
							SELECT e.id_empleado, COUNT(v.id_vehiculo) AS rutinario, v.id_vehiculo
							FROM tcm_empleado AS e
							INNER JOIN tcm_mantenimiento_rutinario AS tmr ON (tmr.id_empleado_repara=e.id_empleado)
							INNER JOIN tcm_vehiculo AS v ON (v.id_vehiculo=tmr.id_vehiculo)
							".$where_fecha." e.id_empleado='".$mecanico."' AND v.id_vehiculo='".$id_vehiculo."'
							GROUP BY v.id_vehiculo
						) AS mtto_rutinario ON (mtto_rutinario.id_empleado=e.id_empleado)
						INNER JOIN tcm_vehiculo AS v ON (mtto_interno.id_vehiculo=v.id_vehiculo OR mtto_rutinario.id_vehiculo=v.id_vehiculo)";
			}
			else /*un mecanico todos vehiculos*/
			{
				if($where_fecha!="") $where_fecha=$where_fecha." AND";
				else $where_fecha="WHERE";
				
				$query="SELECT v.placa, (COUNT(DISTINCT m_interno.fecha) + COUNT(DISTINCT m_rutinario.fecha)) AS mttos
						FROM tcm_vehiculo AS v
						LEFT JOIN
						(
							SELECT tit.id_vehiculo, fecha
							FROM tcm_ingreso_taller AS tit
							INNER JOIN tcm_mantenimiento_interno AS tmi ON (tmi.id_ingreso_taller=tit.id_ingreso_taller)
							".$where_fecha." tmi.id_empleado_mtto='".$mecanico."'
						) AS m_interno ON (m_interno.id_vehiculo=v.id_vehiculo)
						LEFT JOIN
						(
							SELECT tmr.id_vehiculo, fecha
							FROM tcm_mantenimiento_rutinario AS tmr
							".$where_fecha." tmr.id_empleado_repara='".$mecanico."'
						) AS m_rutinario ON (m_rutinario.id_vehiculo=v.id_vehiculo)
						WHERE (v.id_vehiculo=m_interno.id_vehiculo OR v.id_vehiculo=m_rutinario.id_vehiculo)
						GROUP BY v.id_vehiculo";
			}
		}
		elseif($id_vehiculo!='' && $id_vehiculo!=0) /*todos mecanicos un vehiculo*/
		{
			if($where_fecha!="") $where_fecha=$where_fecha." AND";
			else $where_fecha="WHERE";
			
			$query="SELECT e.id_empleado, UPPER(e.nombre) AS nombre, (COALESCE(mtto_interno.interno,'0') + COALESCE(mtto_rutinario.rutinario,'0')) AS mttos, v.placa
					FROM tcm_empleado AS e
					LEFT JOIN
					(
						SELECT e.id_empleado, COUNT(e.id_empleado) AS interno, v.id_vehiculo
						FROM tcm_empleado AS e
						INNER JOIN tcm_mantenimiento_interno AS tmi ON (tmi.id_empleado_mtto=e.id_empleado)
						INNER JOIN tcm_ingreso_taller AS tit ON (tit.id_ingreso_taller=tmi.id_ingreso_taller)
						INNER JOIN tcm_vehiculo AS v ON (v.id_vehiculo=tit.id_vehiculo)
						".$where_fecha." v.id_vehiculo='".$id_vehiculo."'
						GROUP BY e.id_empleado
					) AS mtto_interno ON (mtto_interno.id_empleado=e.id_empleado)
					LEFT JOIN
					(
						SELECT e.id_empleado, COUNT(e.id_empleado) AS rutinario, v.id_vehiculo
						FROM tcm_empleado AS e
						INNER JOIN tcm_mantenimiento_rutinario AS tmr ON (tmr.id_empleado_repara=e.id_empleado)
						INNER JOIN tcm_vehiculo AS v ON (v.id_vehiculo=tmr.id_vehiculo)
						".$where_fecha." v.id_vehiculo='".$id_vehiculo."'
						GROUP BY e.id_empleado
					) AS mtto_rutinario ON (mtto_rutinario.id_empleado=e.id_empleado)
					INNER JOIN tcm_vehiculo AS v ON (mtto_interno.id_vehiculo=v.id_vehiculo OR mtto_rutinario.id_vehiculo=v.id_vehiculo)";
		}
		else/*todos mecánicos con todos vehículos*/
		{
			$query="SELECT e.id_empleado, UPPER(e.nombre) AS nombre, COALESCE(m_interno.interno,'0') AS interno, COALESCE(m_rutinario.rutinario,'0') AS rutinario,
					(COALESCE(m_interno.interno,'0') + COALESCE(m_rutinario.rutinario,'0')) AS mttos
					FROM tcm_empleado AS e
					LEFT JOIN
					(
						SELECT e.id_empleado, COUNT(e.nombre) AS interno, tmi.fecha
						FROM tcm_empleado AS e
						INNER JOIN tcm_mantenimiento_interno AS tmi ON (tmi.id_empleado_mtto=e.id_empleado)
						".$where_fecha."
						GROUP BY e.id_empleado
					) AS m_interno ON (e.id_empleado=m_interno.id_empleado)
					LEFT JOIN
					(
						SELECT e.id_empleado, COUNT(e.nombre) AS rutinario, tmr.fecha
						FROM tcm_empleado AS e
						INNER JOIN tcm_mantenimiento_rutinario AS tmr ON (tmr.id_empleado_repara=e.id_empleado)
						".$where_fecha."
						GROUP BY e.id_empleado
					) AS m_rutinario ON (e.id_empleado=m_rutinario.id_empleado)
					WHERE (COALESCE(m_interno.interno,'0')>0 || COALESCE(m_rutinario.rutinario,'0')>0)";
		}
		
		$query=$this->db->query($query);
		return  $query->result_array();
	}
	/*********************************************************************************************************************************************/
	
	/**********************************FUNCIÓN PARA OBTENER EL/LOS TALLER(ES) EXTERNO(S)***************************************************/
	function consultar_taller_ext($id_taller_externo=NULL)
	{
		$where="";
		if($id_taller_externo!=NULL) $where=" and id_taller_externo='$id_taller_externo'";
		
		$query="SELECT id_taller_externo, nombre as taller FROM tcm_taller_externo WHERE estado=1 ".$where;
		$query=$this->db->query($query);
		return  $query->result_array();
	}
	/*********************************************************************************************************************************************/

}
?>