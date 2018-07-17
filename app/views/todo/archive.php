<div class="row">
	<div class="col-sm-4">
		<p><a href="/"><strong>Today</strong></a></p>
		<p><a href="/next-7-days"><strong>Next 7 days</strong></a></p>
		<h3>Projects</h3>
		<?php if( !empty( $projects ) ): ?>			
		<ul class="list-group">
			<?php foreach ( $projects as $project ): ?>				
			<li class="list-group-item">
				<a href="/projects/<?php echo $project->get_id(); ?>">
					<span class="project-color" style="background-color: <?php echo $project->color; ?>"></span>
					<span class="name"><?php echo $project->name; ?></span>
					<?php if( !empty( $project->tasks ) ): ?>
					<small><?php echo count((array)$project->tasks); ?></small>					
					<?php endif; ?>
				</a>
				<div class="dropdown show float-right">
					<a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						menu
					</a>
					<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
						<a class="dropdown-item" href="/projects/<?php echo $project->get_id(); ?>/edit">Edit</a>
						<a class="dropdown-item" href="/projects/<?php echo $project->get_id(); ?>/delete">Delete</a>
					</div>
				</div>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>		
		
		<p class="mt-2"> 
			<a href="/projects/create">+ Add project</a>				
		</p>	
	</div>
	<div class="col-sm-8">
		<div class="jumbotron">
			<h2>
				Archive	
			</h2>
			<?php if( !empty( $tasks ) ): ?>				
			<ul class="list-group">
				<?php foreach ( $tasks as $task ): ?>					
				<li class="list-group-item">
					<div class="row">
						<div class="col-7">							
						<span class="task-state" style="background-color: <?php echo $task->state_color(); ?>"></span>						
						<span class="name"><?php echo $task->name; ?></span>
						<span class="due-date">(<?php echo date( 'd M H:i', strtotime($task->end_date) ); ?>)</span>
						</div>
						<div class="col-5">							
							<div class="row">
								<div class="col-6 project">
									<span class="project-color" style="background-color: <?php echo $task->projects->color; ?>"></span>
									<span class="name"><?php echo $task->projects->name; ?></span>
								</div>
								<div class="col-6">
									<div class="dropdown show float-right">
										<a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											menu
										</a>
										<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
											<a class="dropdown-item" href="/tasks/<?php echo $task->get_id(); ?>/edit">Edit</a>
											<a class="dropdown-item" href="/tasks/<?php echo $task->get_id(); ?>/delete">Delete</a>
											<a class="dropdown-item" href="/tasks/<?php echo $task->get_id(); ?>/done">Done</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</li>
				<?php endforeach; ?>				
			</ul>
			<?php endif; ?>
			<p class="mt-2"> 
				<a href="/tasks/create">+ Add task</a>				
			</p>
		</div>  
	</div>
</div>