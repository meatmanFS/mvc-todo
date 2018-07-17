<div class="jumbotron">
	<div class="add-task-wrap">
		<h2>
			Add new project
		</h2>
		<hr />
		<div class="card">
			<div class="card-body">
				<form method="POST" action="/projects" >
					<div class="form-group">
						<label for="project_name">Name</label>
						<input type="text" class="form-control" name="project_name" placeholder="Project name">
					</div>			
					<div class="form-group">
						<label for="project_color">Project color</label>
						<input type="color"  class="form-control" name="project_color" value="" />
					</div>					
					<button type="submit" class="btn btn-primary">Add</button>
					<button type="reset" class="btn btn-secondary">Cancel</button>
				</form>		
			</div>
		</div>

	</div>
</div> 
