<section data-settings-section="routing" hidden>
  <h3>Lead routing</h3>
  <p>Control automatic assignment and workload balancing.</p>
  <hr>
  <div class="switch-row">
    <div>
      <strong>Enable smart assignment</strong>
      <small>Use city, availability and current workload</small>
    </div>
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" checked>
    </div>
  </div>
  <div class="switch-row">
    <div>
      <strong>Prioritize best-performing agent</strong>
      <small>Use recent conversion rate as a routing signal</small>
    </div>
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" checked>
    </div>
  </div>
  <div class="switch-row">
    <div>
      <strong>Allow manual override</strong>
      <small>Managers can reassign any lead</small>
    </div>
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" checked>
    </div>
  </div>
  <div class="form-row">
    <label> Maximum active leads per agent <input class="form-control" type="number" value="60">
    </label>
    <label> Unassigned escalation time <select class="form-select">
        <option>15 minutes</option>
        <option>30 minutes</option>
        <option>1 hour</option>
      </select>
    </label>
  </div>
</section>