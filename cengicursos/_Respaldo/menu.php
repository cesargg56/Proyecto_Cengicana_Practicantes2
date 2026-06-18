<?php
require_once "revisar_permisos.php";
function menu_render()
{
    ?>
<div class="container">
		<nav class="navbar navbar-inverse">
	        <div class="navbar-header">
	            <!-- Collapsed Hamburger -->
	            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse-pattern">
	                <span class="sr-only">Toggle Navigation</span>
	                <span class="icon-bar"></span>
	                <span class="icon-bar"></span>
	                <span class="icon-bar"></span>
	            </button>
	           	<span class="navbar-brand">CENGICURSOS</span>
	        </div>
	        <div class="navbar-collapse collapse" id="app-navbar-collapse-pattern">
	            <ul class="nav navbar-nav">
	            	<li role="presentation"><a href="index.php">Inicio</a></li>
	                <!--Inicio sub-menu -->
	                <li class="dropdown">
	                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Usuarios</a>
	                    <ul class="dropdown-menu">
	                        <li role="presentation"><a href="ver_usuarios.php"><span class="glyphicon glyphicon-th-large"></span> Ver Todos</a></li>
	                        <li role="presentation"><a href="agregar_usuarios.php"><span class="glyphicon glyphicon-plus"></span> Agregar</a></li>
	                    </ul>
	                </li>
	                <!--Fin sub-menu -->
	                <!--Inicio sub-menu -->
	                <li class="dropdown">
	                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Ingenios</a>
	                    <ul class="dropdown-menu">
	                        <li role="presentation"><a href="ver_ingenios.php"><span class="glyphicon glyphicon-th-large"></span> Ver Todos</a></li>
	                        <li role="presentation"><a href="agregar_ingenios.php"><span class="glyphicon glyphicon-plus"></span> Agregar</a></li>
	                    </ul>
	                </li>
	                <!--Fin sub-menu -->
	                <!--Inicio sub-menu -->
	                <li role="presentation"><a href="participantes.php">Participantes</a></li>
	                <!--Fin sub-menu -->
	                 <!--Inicio sub-menu -->
	                <li class="dropdown">
	                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Cursos</a>
	                    <ul class="dropdown-menu">
	                    	<li role="presentation" class="dropdown-header"></li>
	                        <li><a href="ver_cursos.php"><span class="glyphicon glyphicon-th-large"></span> Ver Todos</a></li>
	                        <li><a href="agregar_cursos.php"><span class="glyphicon glyphicon-plus"></span> Agregar</a></li>
	                    	<li role="presentation" class="dropdown-header">Categorías</li>
	                        <li><a href="ver_categorias.php"><span class="glyphicon glyphicon-th-large"></span> Ver Todos</a></li>
	                        <li><a href="agregar_categorias.php"><span class="glyphicon glyphicon-plus"></span> Agregar</a></li>
	                        <li role="presentation" class="divider"></li>
	                    </ul>
	                </li>
	                <!--Fin sub-menu -->

	                <!--Inicio sub-menu -->
	                 <li class="dropdown">
	                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Reportes</a>
	                    <ul class="dropdown-menu">
	                    	<li role="presentation" class="dropdown-header">Seleccionar</li>
	                        <li><a href="exportaringenios.php"><span class="glyphicon glyphicon-th-large"></span> Reporte Por Ingenio</a></li>
	                        <li><a href="exportarcursos.php"><span class="glyphicon glyphicon-th-large"></span> Reporte por Curso</a></li>
	                    </ul>
	                </li>
	                <!--Fin sub-menu -->
	                <!--Inicio sub-menu -->
	                 <li role="presentation">
	                    <a href="logout.php?act=logout">Logout</a>
	                </li>
	                <!--Fin sub-menu -->
	            </ul>
	        </div>
	    </nav>
	</div>
<?php }

?>