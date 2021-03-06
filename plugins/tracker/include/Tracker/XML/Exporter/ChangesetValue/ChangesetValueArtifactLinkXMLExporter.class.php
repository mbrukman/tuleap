<?php
/**
 * Copyright (c) Enalean, 2015. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

class Tracker_XML_Exporter_ChangesetValue_ChangesetValueArtifactLinkXMLExporter extends Tracker_XML_Exporter_ChangesetValue_ChangesetValueXMLExporter {

    /**
     * @var PFUser
     */
    private $current_user;

    /**
     * @var Tracker_XML_ChildrenCollector
     */
    private $children_collector;

    public function __construct(
        Tracker_XML_ChildrenCollector $children_collector,
        PFUser $current_user
    ) {
        $this->children_collector = $children_collector;
        $this->current_user       = $current_user;
    }

    protected function getFieldChangeType() {
        return 'art_link';
    }

    public function export(
        SimpleXMLElement $artifact_xml,
        SimpleXMLElement $changeset_xml,
        Tracker_Artifact $artifact,
        Tracker_Artifact_ChangesetValue $changeset_value
    ) {
        $field_xml = $this->createFieldChangeNodeInChangesetNode(
            $changeset_value,
            $changeset_xml
        );

        $children_trackers = $changeset_value->getField()->getTracker()->getChildren();
        $values = $changeset_value->getValue();
        if ($values) {
            array_walk(
                $values,
                array($this, 'appendValueToFieldChangeNode'),
                array(
                    'field_xml'         => $field_xml,
                    'children_trackers' => $children_trackers,
                    'artifact'          => $artifact
                )
            );
        }
    }

    private function appendValueToFieldChangeNode(
        Tracker_ArtifactLinkInfo $artifact_link_info,
        $index,
        $userdata
    ) {
        $field_xml         = $userdata['field_xml'];
        $artifact          = $userdata['artifact'];
        $children_trackers = $userdata['children_trackers'];

        if ($this->canExportLinkedArtifact($artifact, $artifact_link_info, $children_trackers)) {
            $value_xml = $field_xml->addChild('value', $artifact_link_info->getArtifactId());
            $value_xml->addAttribute('nature', $artifact_link_info->getNature());
            $this->children_collector->addChild($artifact_link_info->getArtifactId(), $artifact->getId());
        }
    }

    private function canExportLinkedArtifact(Tracker_Artifact $artifact, Tracker_ArtifactLinkInfo $artifact_link_info, array $children_trackers) {
        if($artifact->getTracker()->isProjectAllowedToUseNature()) {
            $is_a_child = $artifact_link_info->getNature() === Tracker_FormElement_Field_ArtifactLink::NATURE_IS_CHILD;
        } else {
            $is_a_child = in_array($artifact_link_info->getTracker(), $children_trackers);
        }

        return $is_a_child && $artifact_link_info->userCanView($this->current_user);
    }
}
