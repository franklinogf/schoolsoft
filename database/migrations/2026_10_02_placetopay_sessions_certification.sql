-- PlacetoPay WC certification (checklist INS-PR Pago Básico V2)
-- Run once per tenant database.

ALTER TABLE placetopay_sessions
  ADD COLUMN account_id VARCHAR(20) NULL AFTER payable_id,
  ADD COLUMN description VARCHAR(255) NULL AFTER currency,
  ADD COLUMN applied_at DATETIME NULL COMMENT 'When the payment was credited to its payable',
  ADD COLUMN refunded_at DATETIME NULL COMMENT 'When a refund/reversal was applied to its payable',
  ADD UNIQUE INDEX placetopay_sessions_reference_unique (reference),
  ADD INDEX placetopay_sessions_account_status (account_id, status);
