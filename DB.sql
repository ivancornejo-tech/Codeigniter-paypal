
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Payments`
--

CREATE TABLE `Payments` (
  `payment_id` int(11) NOT NULL,
  `PaymentMethod` text COLLATE utf8_unicode_ci NOT NULL,
  `SaleId` int(11) NOT NULL,
  `Total` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `SubTotal` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `PayerMail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `PayerStatus` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `CreateTime` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `UpdateTime` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Payment_state` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `Productos` (
  `idProductos` bigint(20) NOT NULL,
  `Codigo` varchar(255) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Descripcion` varchar(255) NOT NULL,
  `PrecioVenta` decimal(6,2) NOT NULL,
  `PrecioCompra` decimal(6,2) NOT NULL,
  `Existencia` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Productos`
--

INSERT INTO `Productos` (`idProductos`, `Codigo`, `Nombre`, `Descripcion`, `PrecioVenta`, `PrecioCompra`, `Existencia`) VALUES
(1, 'SM-20191', 'Ejemplo 1', '', '0.00', '0.00', '0.00'),
(2, 'SM-20192', 'Ejemplo 2', '', '1500.00', '0.00','2000.00'),
(3, 'SM-20193', 'Ejemplo 3', '', '2500.00', '0.00', '2000.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Productos_Vendidos`
--

CREATE TABLE `Productos_Vendidos` (
  `idPV` bigint(20) NOT NULL,
  `idProductos` bigint(20) UNSIGNED NOT NULL,
  `Cantidad` bigint(20) UNSIGNED NOT NULL,
  `Equipos` int(11) NOT NULL,
  `Precio` decimal(5,2) NOT NULL,
  `idVentas` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `Ventas` (
  `idVentas` bigint(20) UNSIGNED NOT NULL,
  `Fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idPayment` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Payments`
--
ALTER TABLE `Payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indices de la tabla `Productos`
--
ALTER TABLE `Productos`
  ADD PRIMARY KEY (`idProductos`);

--
-- Indices de la tabla `Productos_Vendidos`
--
ALTER TABLE `Productos_Vendidos`
  ADD PRIMARY KEY (`idPV`),
  ADD KEY `idProductos` (`idProductos`),
  ADD KEY `idVentas` (`idVentas`);

--
-- Indices de la tabla `Ventas`
--
ALTER TABLE `Ventas`
  ADD PRIMARY KEY (`idVentas`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

ALTER TABLE `Payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT de la tabla `Productos`
--
ALTER TABLE `Productos`
  MODIFY `idProductos` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `Productos_Vendidos`
--
ALTER TABLE `Productos_Vendidos`
  MODIFY `idPV` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT de la tabla `Registros`
--
ALTER TABLE `Ventas`
  MODIFY `idVentas` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;