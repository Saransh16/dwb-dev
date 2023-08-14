<?php

require_once($CFG->dirroot . '/course/lib.php');

class CategoryCreator {

    private $DB;
    private $logProcessor;

    public function __construct($DB, $logProcessor) {
        $this->DB = $DB;
        $this->logProcessor = $logProcessor;
    }

    private function create_category($name, $idnumber, $parent = 0) {

        // for parent = 0
        // by default the new category will be top level category

        // Check if a category with the same name already exists
        // $existingCategory = $this->DB->get_record('course_categories', ['name' => $name]);

        // if ($existingCategory) {
        //     $this->logProcessor->log("Category {$name} already exists. Skipping creation.");
        //     return $existingCategory->id;
        // }

        $category = new stdClass();
        $category->name = $name;
        $category->parent = $parent; 

        try {
            $categoryId = $this->DB->insert_record('course_categories', $category, true);
            fix_course_sortorder(); // required to build course_categories.depth and .path fields  
            $this->logProcessor->log("Category {$name} created successfully with ID {$categoryId}");
            return $categoryId;
        } catch(Exception $e) {
            $this->logProcessor->log("Category {$name} not created.");
            $this->logProcessor->handleException($e);
            return false;
        }
    }

    public function create_categories_hierarchy() {

        $types = unserialize(PRODUCT_TYPE);
        $years = unserialize(YEARS);
        $subjects = unserialize(SUBJECTS);
        $terms = unserialize(TERMS);
        $grades = unserialize(GRADES);    
        
        foreach ($types as $type) { 

            $parentCategoryId = $this->create_category($type, $type);
            if (!$parentCategoryId) continue; // If failed to create category, skip to next iteration.

            foreach ($years as $year) {
                $yearId = $this->create_category($year, $type . '_' . $year, $parentCategoryId);                
                if (!$yearId) continue;            

                foreach ($terms as $term) {
                    $termId = $this->create_category($term, $type . '_' . $year . '_' . $term, $yearId);                    
                    if (!$termId) continue;

                    foreach ($grades as $grade) {
                        $gradeId = $this->create_category($grade, $type . '_' . $year . '_' . $term . '_' . $grade, $termId);
                        if (!$gradeId) continue;

                        foreach($subjects as $subject) {
                            $subjectId = $this->create_category($subject, $subject . '_' . $grade, $gradeId);
                            if (!$subjectId) continue;
                        }
                    }
                }
            }
        }
    }
}

?>