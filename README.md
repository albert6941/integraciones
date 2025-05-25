# 🚛 Integraciones con API Wialon y SOAP (Soluciones al transporte )

Este proyecto contiene scripts en PHP para integrar y consultar datos de unidades de transporte a través de las APIs de Wialon y servicios SOAP de terceros.

## 📌 Descripción

- Se realizan consultas a la API de Wialon para obtener información de unidades (posición, velocidad, nombre, etc.).
- Se consumen servicios SOAP para obtener tokens de acceso y trabajar con proveedores como Freddy o Patino Herrera.
- La información procesada se devuelve en formato JSON.
- Seguridad implementada a nivel de servidor (`.htaccess`) y configuración PHP para evitar ataques comunes.

---

## 📁 Estructura de Archivos
├── config.php # Configuración de tokens y credenciales
├── index.php # Consulta principal a API Wialon (token ABC)
├── jobid.php # Obtención de token SOAP y uso del token Wialon RC
├── wialon/ # Clase Wialon para interacción con la API
├── Recurso #carpeta con mas integraciones 
└── .htaccess # Configuraciones de seguridad en Apache

## 🔐 Seguridad

Incluye medidas de seguridad como:

- Protección contra XSS, inyecciones y bots maliciosos en `.htaccess`.
- Tokens sensibles y credenciales protegidos en `config.php`.
- Refrescamiento automático de página con temporizador para mantener actualizaciones constantes.

---

## 🧩 Dependencias

- Servidor Apache con soporte para mod_headers y mod_rewrite.
- PHP 7+ con soporte para SOAP.
- Acceso a Wialon Hosting y tokens válidos.
- Internet activo para consumir las APIs externas.

---

## 🚀 Cómo ejecutar

1. Clonar el repositorio
2. Colocar los archivos en un servidor Apache o entorno XAMPP.
3. Editar config.php y agregar los tokens actualizados (recuerda que los de Wialon expiran cada 30 días).
4. Abrir index.php o nombrePlaca.php en el navegador para probar la integración.

