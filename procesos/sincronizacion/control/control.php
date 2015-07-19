<?php
	include "../db/funcionesdb.php";
	$funsion = $_POST['funsion'];
	// $funsion = "sincClientes";
	switch ($funsion) {
		case 'sincClientes': sincClientes(); break;
		case 'sincPropiedades': sincPropiedades(); break;
			
		
		default:
			# code...
			break;
	}
	function sincClientes(){
		$xmlClientes = simplexml_load_file("xml/xml20150716_ClientesXML.xml");
		$objeto = (object) array();

		   foreach ($xmlClientes as $xml){

		    	$arrendatario = (object) array(
					'a'.$xml->IDCliente => (object) array(
						'fechaRegistro' => $xml->Fechaderegistro,
						'clave' => $xml->IDCliente,
						'tipoCliente' => '1',
						'nombre' => $xml->RazonSocial,
						'Email' => $xml->Email,
						'telefono' => '00000000',
						'rfc' => $xml->RFC,
						'direccion' => $xml->DireccionPrincipalCalle,
						'ext' => $xml->DireccionPrincipalNumExt,
						'int' => $xml->DireccionPrincipalNumInt,
						'cp' => $xml->DireccionPrincipald4,
						'colonia' => $xml->DireccionPrincipald8,
						'ciudad' => $xml->DireccionPrincipald7,
						'estado' => $xml->DireccionPrincipald5,
						'moroso' => $xml->Moroso,
						'metododepago' => $xml->Metododepago,
						'contacto' => (object) array(
							'c'.$arrayClientes[0]->Clave => (object) array(
								'clave_contacto' => $xml->Clave,
								'clave_padre' => $xml->IDCliente,
								'tipoCliente' => $xml->TipodeContacto,
								'nombre' => $xml->NombreNombre." ".$xml->NombreApellidoPaterno." ".$xml->NombreApellidoMaterno,
								'Email' => $xml->email,
								'tipoCliente' => $xml->TipodeContacto,
								'telefonoCasa' => "(".$xml->TelefonoCasaClaveLD.") ".$xml->TelefonoCasaTelefono,
								'telefonoCel' => "(".$xml->MovilClaveLD.") ".$xml->MovilTelefono,
								'telefonoOficina' => "(".$xml->TelefonoTrabajoClaveLD.") ".$xml->TelefonoTrabajoTelefono,
								'direccion' => $xml->DireccionContactoCalle,
								'ext' => $xml->DireccionContactoNumExt,
								'int' => $xml->DireccionContactoNumInt,
								'cp' => $xml->DireccionContactod4,
								'colonia' => $xml->DireccionContactod8,
								'ciudad' => $xml->DireccionContactod7,
								'estado' => $xml->DireccionContactod5,
							),
						),
						'ultimaFecha' => $xml->UltimaModificacion
					)
				);
	 			$arrayClientes[]=$arrendatario;
	 			// $clientesJSON = json_encode($arrayClientes);
				
			}

		    $regFlag = 1;
		    // registraEvento("clientes","descarga",$clientes->ultimaFecha);
			foreach ($arrayClientes as $key => $reg) {
				foreach ($reg as $key => $value) {
					// $clientesJSON = json_encode($value);
					$ultimaSinc = ultimaSinc();
					$fecha1 = new DateTime($value->ultimaFecha);
					$fecha2 = new DateTime($ultimaSinc);
					$interval = $fecha1->diff($fecha2); // fecha2 - fecha1
					
					if($interval->invert=="1"){ // [invert] => 0 [y] => 0 [m] => 0 [d] => 0 [h] => 1 [i] => 0 [s] => 0 
						$resultado = insertaClientes($value);
						if($resultado == 1 && $regFlag==1){
							registraEvento("clientes","descarga",$value->ultimaFecha);
							$regFlag=0;
						}
						echo "%########%";
						foreach ($value->contacto as $key => $subvalue) {
							if($subvalue->clave_contacto>0){
								$res=insertaContactos($subvalue,$value->ultimaFecha);
								if($res == 1 && $regFlag==1){
									registraEvento("contactos","descarga",$value->ultimaFecha);
									$regFlag=0;
								}
							}
							
						}
						
					}

				
				echo "<br>";	
				}
					
			}
			
			// print_r($arrendatario);
			// echo "-----------";
		
	}

	function sincPropiedades(){
		$xmlPropiedades = simplexml_load_file("xml/xml20150716_PropiedadesXML.xml");

		foreach ($xmlPropiedades as $key => $value) {
			$resultado = insertaPropiedades($value);
		}
		// fechaRegistro
		// clave 				.Propiedad
		// nombre 				.NombrePropiedad
		// tipo_propiedad 		.TipodePropiedadTipodepropiedadCodigo TipodePropiedadTipodepropiedadDescripcion
		// propietario 		.PropietarioPropietariosCodigo PropietarioPropietariosNombre
		// paga_mantenimiento 	.Pagamantenimiento
		// direccion 			.DireccionCalle
		// ext 				.DireccionNumExt
		// int 				.DireccionNumInt
		// colonia 			.Direcciond8
		// cp 					.Direcciond4
		// ciudad 				.Direcciond7
		// estado 				.Direcciond6 Direcciond5
		// valor_inicial
		// tipo_moneda
		// cambio
		// adquisicion
		// valor_actual
		// monto_inquilino


	// 	-----


	// <Direcciond3></Direcciond3>
	// <Direcciond8>INSURGENTES,MIXCOAC</Direcciond8>
	// <Direcciond7>BENITO JUAREZ</Direcciond7>
	
	// <Direcciond5>Distrito Federal</Direcciond5>
	
	// <Rentado></Rentado>
	}
			
	function arrglaFecha($fechaMal){
		$fechaXML = "13.Oct.14 17:35:04";
		$s = explode(' ', $fechaMal);
		$fecha = explode('.',$s[0]);
		$dia = $fecha[0];
		$mesLetra = $fecha[1];
		$anio4d = "20".$fecha[2];
		// echo $interval->format('%R%a')+0;
		switch ($mesLetra) {
			case 'Ene': $mes = '01'; break;
			case 'Feb': $mes = '02'; break;
			case 'Mar': $mes = '03'; break;
			case 'Abr': $mes = '04'; break;
			case 'May': $mes = '05'; break;
			case 'Jun': $mes = '06'; break;
			case 'Jul': $mes = '07'; break;
			case 'Ago': $mes = '08'; break;
			case 'Sep': $mes = '09'; break;
			case 'Oct': $mes = '10'; break;
			case 'Nov': $mes = '11'; break;
			case 'Dic': $mes = '12'; break;
			
			default:
				# code...
				break;
		}
		if(isset($s[1])){
			return $anio4d."-".$mes."-".$dia." ".$s[1];
		}
		return $anio4d."-".$mes."-".$dia;
			
	}
?>

<?php
//////  SQL  /////

/*
id 					.[IDCliente] => 11 
fechaRegistro 		.[Fechaderegistro] => 01.Oct.13 
clave 				.[Clave] => 0 
clave_padre			.---
tipoCliente 		.[TipodeContacto] => 
nombre 				.[RazonSocial] => HIGH MANAGEMENT SOLUTIONS 
Email 				.[Email] => paul@hmsolutions.mx 
telefono 			.
rfc 				.[RFC] => Array ( [0] => HSO120813B92 
direccion 			.[DireccionCalle] => Array ( [0] => CERRADA VALLE DE LA PLATA
ext 				.[DireccionNumExt] => Array ( [0] => 12 
int 				.[DireccionNumInt] => Array ( [0] => 
cp 					.[Direcciond4] => Array ( [0] => 76269 
colonia				.[Direcciond8] => Array ( [0] => LA PRADERA
ciudad 				.[Direcciond7] => Array ( [0] => EL MARQUES
estado 				.[Direcciond5] => Array ( [0] => QUERETARO
moroso 				.[Moroso] => 
metododepago 		.[Metododepago] => NO IDENTIFICADO 
					.[NombreNombre] => 
					.[NombreApellidoPaterno] => 
					.[NombreApellidoMaterno] => 
					.[email] => 
					.[TipodeContacto] => 
					.[TelefonoCasaClaveLD] => 
					.[TelefonoCasaTelefono] => 
					.[MovilClaveLD] => 
					.[MovilTelefono] => 
					.[TelefonoTrabajoClaveLD] => 
					.[TelefonoTrabajoTelefono] => S
					.[UltimaModificacion] => 13.Oct.14 17:35:04

/////  XML  /////

SimpleXMLElement Object ( 
	[IDCliente] => 11 
	[Fechaderegistro] => 01.Oct.13 
	[Activo] => Si 
	[RazonSocial] => HIGH MANAGEMENT SOLUTIONS 
	[RFC] => Array ( 
		[0] => HSO120813B92 
		[1] => SimpleXMLElement Object ( ) 
	) 
	[DireccionCalle] => Array ( 
		[0] => CERRADA VALLE DE LA PLATA 
		[1] => SimpleXMLElement Object ( ) 
	) 
	[DireccionNumExt] => Array ( 
		[0] => 12 [1] => SimpleXMLElement Object ( ) 
	) 
	[DireccionNumInt] => Array ( 
		[0] => SimpleXMLElement Object ( ) 
		[1] => SimpleXMLElement Object ( ) 
	) 
	[Direcciond4] => Array ( 
		[0] => 76269 
		[1] => SimpleXMLElement Object ( ) 
	) [Direcciond3] => Array ( 
		[0] => SimpleXMLElement Object ( ) 
		[1] => SimpleXMLElement Object ( ) 
	) 
	[Direcciond8] => Array ( 
		[0] => LA PRADERA [1] => SimpleXMLElement Object ( ) 
	) 
	[Direcciond7] => Array ( [0] => EL MARQUES [1] => SimpleXMLElement Object ( ) ) 
	[Direcciond6] => Array ( [0] => SimpleXMLElement Object ( ) [1] => SimpleXMLElement Object ( ) ) 
	[Direcciond5] => Array ( [0] => QUERETARO [1] => SimpleXMLElement Object ( ) ) 
	[Direcciond10] => Array ( [0] => MÉXICO [1] => SimpleXMLElement Object ( ) ) 
	[Email] => paul@hmsolutions.mx 
	[UltimaModificacion] => 13.Oct.14 17:35:04 
	[Metododepago] => NO IDENTIFICADO 
	[Nodecuenta] => 1234 
	[Banco] => SimpleXMLElement Object ( ) 
	[PaginaWeb] => SimpleXMLElement Object ( ) 
	[VentasaCredito] => No 
	[DiasdeCredito] => 30 
	[Descuento] => % 
	[IVA] => Si 
	[FactorIVA] => 1 
	[PermiteFacturar] => Si 
	[FechadeActualizacion] => 13.Oct.14 17:35:04 
	[Moroso] => SimpleXMLElement Object ( ) 
	[Factura] => SimpleXMLElement Object ( ) 
	[Clave] => 0 
	[NombreNombre] => SimpleXMLElement Object ( ) 
	[NombreApellidoPaterno] => SimpleXMLElement Object ( ) 
	[NombreApellidoMaterno] => SimpleXMLElement Object ( ) 
	[email] => SimpleXMLElement Object ( ) 
	[TipodeContacto] => SimpleXMLElement Object ( ) 
	[TelefonoCasaClaveLD] => SimpleXMLElement Object ( ) 
	[TelefonoCasaTelefono] => SimpleXMLElement Object ( ) 
	[MovilClaveLD] => SimpleXMLElement Object ( ) 
	[MovilTelefono] => SimpleXMLElement Object ( ) 
	[TelefonoTrabajoClaveLD] => SimpleXMLElement Object ( ) 
	[TelefonoTrabajoTelefono] => SimpleXMLElement Object ( ) )




<Propiedades>
	<Propiedad>1</Propiedad>
	<NombrePropiedad>PATRIOTISMO</NombrePropiedad>
	<PropietarioPropietariosCodigo>1</PropietarioPropietariosCodigo>
	<PropietarioPropietariosNombre>KALILHEL</PropietarioPropietariosNombre>
	<TipodePropiedadTipodepropiedadCodigo>1</TipodePropiedadTipodepropiedadCodigo>
	<TipodePropiedadTipodepropiedadDescripcion>LOCAL COMERCIAL</TipodePropiedadTipodepropiedadDescripcion>
	<Pagamantenimiento></Pagamantenimiento>
	<DireccionCalle>AV. MUNICH</DireccionCalle>
	<DireccionNumExt>823</DireccionNumExt>
	<DireccionNumInt>PB</DireccionNumInt>
	<Direcciond4>03920</Direcciond4>
	<Direcciond3></Direcciond3>
	<Direcciond8>INSURGENTES,MIXCOAC</Direcciond8>
	<Direcciond7>BENITO JUAREZ</Direcciond7>
	<Direcciond6>Ciudad de México</Direcciond6>
	<Direcciond5>Distrito Federal</Direcciond5>
	<Direcciond10>Mexico</Direcciond10>
	<Rentado></Rentado>
</Propiedades>









SimpleXMLElement Object ( [IDCliente] => 45 [Fechaderegistro] => 01.Oct.13 [Activo] => Si [RazonSocial] => 7-ELEVEN MEXICO SA DE CV [RFC] => Array ( [0] => SEM980701STA [1] => SimpleXMLElement Object ( ) ) [DireccionCalle] => Array ( [0] => AV. MUNICH [1] => SimpleXMLElement Object ( ) ) 
[DireccionNumExt] => Array ( [0] => 195 [1] => SimpleXMLElement Object ( ) ) [DireccionNumInt] => Array ( [0] => B [1] => SimpleXMLElement Object ( ) ) [Direcciond4] => Array ( [0] => 66450 [1] => SimpleXMLElement Object ( ) ) 
[Direcciond3] => Array ( [0] => SimpleXMLElement Object ( ) [1] => SimpleXMLElement Object ( ) ) [Direcciond8] => Array ( [0] => CUAUHTÉMOC [1] => SimpleXMLElement Object ( ) ) [Direcciond7] => Array ( [0] => SAN NICOLÁS DE LOS GARZA [1] => SimpleXMLElement Object ( ) ) [Direcciond6] => Array ( [0] => SAN NICOLÁS DE LOS GARZA [1] => SimpleXMLElement Object ( ) ) [Direcciond5] => Array ( [0] => NUEVO LEÓN [1] => SimpleXMLElement Object ( ) ) [Direcciond10] => Array ( [0] => MÉXICO [1] => MÉXICO ) 
[Email] => luis.valle@7-eleven.com.mx [UltimaModificacion] => 16.Jul.15 18:58:57 [Metododepago] => TRANSFERENCIA ELECTRÓNICA DE FONDOS [Nodecuenta] => 4051 [Banco] => BANAMEX [PaginaWeb] => SimpleXMLElement Object ( ) [VentasaCredito] => No [DiasdeCredito] => 0 [Descuento] => % [IVA] => Si [FactorIVA] => 1 [PermiteFacturar] => Si [FechadeActualizacion] => 16.Jul.15 18:58:57 [Moroso] => SimpleXMLElement Object ( ) [Factura] => SimpleXMLElement Object ( ) 
[Clave] => 1 
[NombreNombre] => N1 
[NombreApellidoPaterno] => AP1 
[NombreApellidoMaterno] => AM1 
[email] => SimpleXMLElement Object ( ) 
[TipodeContacto] => CONTACTO 
[TelefonoCasaClaveLD] => SimpleXMLElement Object ( ) [TelefonoCasaTelefono] => SimpleXMLElement Object ( ) 
[MovilClaveLD] => SimpleXMLElement Object ( ) [MovilTelefono] => SimpleXMLElement Object ( ) 
[TelefonoTrabajoClaveLD] => SimpleXMLElement Object ( ) [TelefonoTrabajoTelefono] => SimpleXMLElement Object ( ) )






*/

?>