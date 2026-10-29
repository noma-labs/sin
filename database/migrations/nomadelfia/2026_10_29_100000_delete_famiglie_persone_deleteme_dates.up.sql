-- Run the migrations

-- The dates of entrata and uscita of a persona in a famiglia were renamed deleteme_ by DeleteFamilyDates (2023_11_09_171412)
-- and nothing reads them anymore.
ALTER TABLE `famiglie_persone` DROP COLUMN `deleteme_data_entrata`, DROP COLUMN `deleteme_data_uscita`;
