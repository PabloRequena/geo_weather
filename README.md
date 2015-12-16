# geo_weather
RUNATOR PRUEBA

Requisitos mínimos:
-	PHP 5.5 o superior
-	MySql
-	Entorno Apache con soporte para PHP/MySql
-	Composer

Sugerencias:
-	Symfony2 

Instalación:

  1.	Descargar el código de Github:
    
    git clone https://github.com/PabloRequena/geo_weather.git
    
  2.	Instalar dependencias vendor mediante composer:
    
    composer install
    
    Esto te hará una serie de preguntas sobre la configuración de la base de datos de la aplicación y otras preguntas sobre la configuración de variables a usar.

3.	Si disponemos de symfony2 instalado podremos usar el comando pertinente para crear y actualizar la base de datos
    
    php bin/console doctrine:database:create
    
    php bin/console doctrine:schema:update –force
    
4.	Si no disponemos de symfony2 instalado deberemos ejecutar el script sql existente en la carpeta bd del proyecto en GitHub.

Uso:

	Podremos probar el servicio accediendo mediante la url:
    
    http://localhost/geo_weather/web/geoweather/{fecha}/{hora}/{lat}/{lon}

  Dejo un ejemplo:
  
    http://localhost/geo_weather/web/geoweather/2015-12-01/12:00:00/39.458555/-0.391828

  Esto nos mostrará los valores devueltos en formato JSON y nos almacenará dichos datos en nuestra base de datos.
