<?php
	class Imagen
	{
		public $error;
		
		public static function thumb_jpeg($imagen, $destino, $anchura = 150)
		{
			if (!$datos = getimagesize($imagen))
				return false;
			$ancho_origen = $datos[0];
			$alto_origen = $datos[1];
			if ($ancho_origen < $anchura or $alto_origen < $anchura)
			{
				$_SESSION['error_foto_receta'] = 'El ancho y alto de la imagen no pueden ser menores a ' 
						. $anchura . ' p�xeles';
				return false;
			}
			$x = $y = 0;
			$res = $ancho_origen - $alto_origen;
			if ($res)
			{
				//la imagen no es un cuadrado, luego hay que recortarla para que lo sea
				if ($res > 0)
				{
					//es m�s ancha que alta
					$x = round($res / 2);
					$ancho_origen = $alto_origen;
				}
				else
				{
					//es m�s alta que alta
					$y = round(abs($res) / 2);
					$alto_origen = $ancho_origen;
				}
			}
			/*
			$ratio = ($ancho_origen / $anchura);
			$altura = round($alto_origen / $ratio);
			*/
			$altura = $anchura;
			if (!$thumb = imagecreatetruecolor($anchura, $altura))
				return false;
			if (!$img = imagecreatefromjpeg($imagen))
				return false;
			if (!imagecopyresampled ($thumb, $img, 0, 0, $x, $y, $anchura, $altura, $ancho_origen, $alto_origen))
				return false;
			if (!imagejpeg($thumb, $destino))
				return false;
			return true;
		}
		
		public function valida_foto($foto, $tam_min = null)
		{
			if ($foto['type'] != 'image/jpeg' and $foto['type'] != 'image/jpg' 
					and $foto['type'] !=  'image/pjpeg')
			{
				$this->error = 'S�lo fotos en formato JPG';
				return false;
			}
			if ($foto['size'] > 2000000)
			{
				$this->error = 'El tama�o de la foto excede el m�ximo permitido';
				return false;
			}
			if ($tam_min)
			{
				if (!$datos = getimagesize($foto['tmp_name']))
					return false;
				if ($datos[0] < $tam_min or $datos[1] < $tam_min)
				{
					$this->error = 'El ancho y alto de la imagen no pueden ser menores a ' . $tam_min 
							. ' p�xeles';
					return false;
				}
			}
			if (!@is_uploaded_file($foto['tmp_name']))
			{
				$this->error = 'No se ha podido subir la imagen';
				return false;
			}
			return true;
		}
		
		public function upload_foto($foto, $destino, $tam_min = null)
		{
			if (!$this->valida_foto($foto, $tam_min))
				return false;
			if (!@move_uploaded_file($foto["tmp_name"], $destino))
			{
				$this->error = 'No se ha podido mover la imagen';
				return false;
			}
			return true;
		}
		
		public function error()
		{
			return $this->error;
		}
	}