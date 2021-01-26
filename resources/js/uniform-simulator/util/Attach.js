class Attach {
	constructor({
		active, 
		name, 
		image, 
		size, 
		type, 
		width, 
		height, 
		isFront, 
		classname, 
		x = 150, y = 200, angle = 0, 
		locked = false
	}) {
		this.active = active;
		this.name = name;
		this.image = image;
		this.size = size;
		this.type = type;
		this.width = 100;
		this.height = (height / width) * 100;
		this.isFront = isFront;
		this.classname = classname;
		this.x = x;
		this.y = y;
		this.angle = angle;
		this.locked = locked;
	}
}

export default Attach;