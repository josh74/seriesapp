12/05/2013
Se ha reorganizado el c�digo siguiendo el patr�n MVC. De esta forma
se diferencia entre el modelo, las vistas y el control, permitiendo tener 
m�s orden en el desarrollo.
Tambi�n se han eliminado ficheros innecesarios.
En cada uno de los ficheros se explica la idea de cada uno, pero de forma
b�sica podr�a resumirse as�:

index.php 
es el punto de entrada, "activa" el controlador.

seriesMaster.php
se sirve de la l�gica del modelo para mostrar las vistas con la informaci�n pertinente.

seriesMaster.php
implementa m�todos de an�lisis de DOM, de generaci�n y selecci�n de datos y cualquier funci�n de proceso que se pudiera necesitar.

seriesView.php
se recogen las vistas de la aplicaci�n. Todo lo que signifique mostrar informaci�n se implementa aqu�. Tambi�n se recoge en cada vista la posible informaci�n a pasar a otras etapas.

El planteamiento es bastante aproximado y se ir� puliendo, pero permite separar el desarrollo e incorporar f�cilmente cualquier c�digo en el sitio adecuado.


10/05/2013
Estos son los ficheros basicos de esta nueva version
index.html y seriesApp.js son la parte del cliente.
index.html da la estructura, y seriesApp.js tiene como mision modificar este
documento.
seriesMaster.php tiene que resolver los problemas de comunicacion con el
servidor de series, captar el DOM, extraer informacion, para pasarla a la
parte del cliente. Tambien podria generar el codigo para el player, etc.
Cualquier critica, sugerencia, aportacion, modificacion, sera bienvenida. Yo
diria que incluso aplaudida y todo, fijate... ;)
