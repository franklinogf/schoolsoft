-- Auto pagos con PlacetoPay (subscription token + collect)
-- Run once per tenant database.

ALTER TABLE posteos
  ADD COLUMN pp_token TEXT NULL COMMENT 'PlacetoPay card token (subscription)',
  ADD COLUMN pp_subtoken VARCHAR(64) NULL,
  ADD COLUMN pp_last4 CHAR(4) NULL,
  ADD COLUMN pp_brand VARCHAR(30) NULL,
  ADD COLUMN pp_nombre VARCHAR(100) NULL COMMENT 'Payer name sent on collect',
  ADD COLUMN pp_apellido VARCHAR(100) NULL,
  ADD COLUMN estado VARCHAR(12) NOT NULL DEFAULT 'pendiente' COMMENT 'pendiente, activo, fallido, cancelado',
  ADD COLUMN ultimo_cobro DATE NULL,
  ADD INDEX posteos_estado_dia (estado, diaDePago);

-- Cards/ACH stored in clear text can no longer be used: parents must register
-- their card again through PlacetoPay. Run this only after the legacy job is off.
-- UPDATE posteos SET ccNumero = NULL, ccNombre = NULL, fechaExpiracion = NULL, ccZip = NULL,
--   achNombre = NULL, achNumero = NULL, numeroRuta = NULL, tipoCuenta = NULL, achZip = NULL;
