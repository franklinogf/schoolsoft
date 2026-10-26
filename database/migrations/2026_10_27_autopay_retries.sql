-- Auto pagos: reintentos y aviso previo
-- Run once per tenant database.

ALTER TABLE posteos
  ADD COLUMN intentos TINYINT NOT NULL DEFAULT 0 COMMENT 'Consecutive rejected charge attempts',
  ADD COLUMN proximo_intento DATE NULL COMMENT 'When to retry a rejected charge',
  ADD COLUMN aviso_enviado DATE NULL COMMENT 'Last pre-charge reminder sent';
