DELIMITER //
CREATE OR REPLACE PROCEDURE liquidacion_combustible()
BEGIN

	SELECT *, (q.disponible - q.consumido) AS residuo_enero FROM (
	SELECT r.id_requisicion, r.mes, sr.restante_anterior, r.entregado, (sr.restante_anterior + entregado) AS disponible, COALESCE(c.cant,0) AS consumido, r.id_seccion, s.nombre_seccion
	FROM (SELECT *, SUM(cantidad_entregado) entregado FROM `tcm_requisicion` WHERE YEAR(fecha_entregado) >= '2018' GROUP BY id_seccion, mes ORDER BY mes) AS r 
	JOIN org_seccion AS s ON s.id_seccion = r.id_seccion 
	LEFT JOIN tcm_sobrante_reset AS sr ON sr.id_seccion = r.id_seccion
	LEFT JOIN tcm_consumido_mes c ON c.mesc=r.mes AND c.id_fuente_fondo=r.id_fuente_fondo AND c.id_seccion=r.id_seccion ORDER BY r.mes, s.nombre_seccion
	) AS q ORDER BY q.mes ;

END;
//;

CALL liquidacion_combustible();