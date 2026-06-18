<?php
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
	                <!--Inicio sub-menu -->
	                <li role="presentation"><a href="participantes.php">Participantes</a></li>
	                <!--Fin sub-menu -->
	                <!--Inicio sub-menu -->
	                 <li class="dropdown">
	                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" onclick="javascript:alert('Opción No disponible');">Reportes</a>
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