-- Update record revision numbers for a region, so they display in Perch.
-- Replace :regionID and :revisionNum

UPDATE perch2_content_items pci
INNER JOIN
(
  SELECT 
    itemID, 
    MAX(itemRev) AS itemRevMax 
  FROM perch2_content_items 
  WHERE regionID = :regionID 
  GROUP BY itemID
) AS max
ON pci.itemID = max.itemID AND pci.itemRev = max.itemRevMax
SET pci.itemRev = :revisionNum