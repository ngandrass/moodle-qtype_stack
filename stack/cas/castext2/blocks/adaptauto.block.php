<?php
// This file is part of STACK
//
// STACK is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// STACK is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with STACK.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This class adds in the "adapt auto" blocks to castext.
 * @package    qtype_stack
 * @copyright  2025 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../../../utils.class.php');

// Register a counter.
require_once(__DIR__ . '/iframe.block.php');
stack_cas_castext2_iframe::register_counter('///ADAPTAUTO_COUNT///');

class stack_cas_castext2_adaptauto extends stack_cas_castext2_block {

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {
        $body = new MP_List([new MP_String('%root')]);

        $code = 'import {stack_js} from "' . stack_cors_link('stackjsiframe.min.js') . '";';
        $code .= 'document.addEventListener("DOMContentLoaded", function(){';
        if (isset($this->params['show_ids'])) {
            $splitshowid = preg_split ("/[\ \n\;]+/", $this->params['show_ids']);
            foreach ($splitshowid as &$id) {
                $code .= "stack_js.toggle_visibility('stack-adapt-" . $id . "',true);";
            }
        }
        if (isset($this->params['hide_ids'])) {
            $splithideid = preg_split ("/[\ \n\;]+/", $this->params['hide_ids']);
            foreach ($splithideid as &$id) {
                $code .= "stack_js.toggle_visibility('stack-adapt-" . $id . "',false);";
            }
        }
        $code .= '});';

        // Now add a hidden [[iframe]] with suitable scripts.
        $body->items[] = new MP_List([
            new MP_String('iframe'),
            new MP_String(json_encode([
                'hidden' => true,
                'title' => 'Logic container for a revealing portion ///ADAPTAUTO_COUNT///.',
            ])),
            new MP_List([
                new MP_String('script'),
                new MP_String(json_encode(['type' => 'module'])),
                new MP_String($code),
            ])
        ]);

        return $body;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function is_flat(): bool {
        // Never flat, the [[iframe]] portion needs extra processing.
        return false;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function postprocess(array $params, castext2_processor $processor, castext2_placeholder_holder $holder): string {
        return 'Post processing of reveal adaptauto never happens, this block is handled through [[iframe]].';
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate_extract_attributes(): array {
        return [];
    }
}
