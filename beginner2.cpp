#include <iostream>
#include <algorithm>
#include <GL/glut.h>

using namespace std;

GLfloat lightAmb[] = { 0.f, 0.f, 0.f, 1.f };
GLfloat lightDiff[] = { 1.f, 1.f, 1.f, 1.f };
GLfloat lightPos[] = { 1.f, 1.f, 1.f, 0.f };

GLfloat red[] = { 1.f, 0.f, 0.f, 1.f };
GLfloat green[] = { 0.f, 1.f, 0.f, 1.f };
GLfloat white[] = { 1.f, 1.f, 1.f, 1.f };
GLfloat table_color[] = { 0.10, 0.32, 0.35 };
//GLfloat blue[] = { 0.f, 0.f, 1.f, 1.f };
//GLfloat black[] = { 0.f, 0.f, 0.f, 1.f };

#define W 10 /* 1/2 the width of the stand */
#define D 15 /* 1/2 the length of the table */
#define H 0.5 /* wall height */
#define R 0.3 /* Pack radius */

float x_pos = 0.0, y_pos = 0.0;
double angle = 0.0;

int x_0 = 0, y_0 = 0;
int x_1 = 0, y_1 = 0;
int direction_x = 0;
int direction_y = 0;

float Vx = 0.f, Vy = 0.f;
float Vmax = 1.0;
float hypothesis = 0.f;

float accel = 1.0;
float radius = 1.0;

class BallObject {
public:
	string name;
	float radius;
	float x_pos;
	float y_pos;
	float accel;
	float Vx;
	float Vy;
	bool swt;
	int direction_x;
	int direction_y;

	BallObject(int radius, float x_pos, float y_pos, float accel, float Vx, float Vy, int dir_x, int dir_y, bool swt) {
		this->radius = radius;
		this->x_pos = x_pos;
		this->y_pos = y_pos;
		this->accel = accel;
		this->Vx = Vx;
		this->Vy = Vy;
		this->direction_x = dir_x;
		this->direction_y = dir_y;
		this->swt = swt;
	}

};

static void myGround(double height);
void pockets();
bool collision(float& x_1, float& y_1, float& x_2, float& y_2);
bool putInPocket(float& x_1, float& y_1);
void display();
void idle();
void timer(int state);
void ball_motion(BallObject* obj, int type);
void mouse(int button, int state, int x, int y);
void dragMotion(int x, int y);
void passiveMotion(int x, int y);
void keyboard(unsigned char key, int x, int y);
void resize(int w, int h);
void init();

BallObject* red_ball = new BallObject(radius, 0.0, 0.0, 1.0, 0.0, 0.0, 1, 1, false);
BallObject* white_ball = new BallObject(radius, 5.0, 5.0, 1.0, 0.0, 0.0, 1, 1, false);

int main(int argc, char** argv) {

	red_ball->name = "RED";
	white_ball->name = "WHITE";

	glutInit(&argc, argv);
	glutInitDisplayMode(GLUT_RGBA | GLUT_DOUBLE);

	glutInitWindowPosition(350, 250);
	glutInitWindowSize(750, 500);
	glutCreateWindow("Practice Window");

	glutDisplayFunc(display);
	glutReshapeFunc(resize);
	glutKeyboardFunc(keyboard);
	// glutKeyboardUpFunc(keyboardUp);

	glutMouseFunc(mouse);
	glutMotionFunc(dragMotion);
	// glutPassiveMotionFunc(passiveMotion);

	init();
	glutMainLoop();
}

static void myGround(double height) {
	const static GLfloat ground[][4] = {/* base color */
	  {0.2, 0.2, 0.2, 1.0 },
	  {0.3, 0.3, 0.3, 1.0}
	};

	int i, j;

	glBegin(GL_QUADS);

	/* Draw the floor */
	glNormal3d(0.0, 1.0, 0.0);
	for (j = -W - 1; j <= W; ++j) {
		for (i = -D; i <= D + 1; ++i) {
			glColor3f(0.10, 0.32, 0.35);
			glVertex3d((GLdouble)i, height, (GLdouble)j);
			glVertex3d((GLdouble)i, height, (GLdouble)(j + 1));
			glVertex3d((GLdouble)(i + 1), height, (GLdouble)(j + 1));
			glVertex3d((GLdouble)(i + 1), height, (GLdouble)j);
		}
	}

	glEnd();
}

void pockets() {

	glPushMatrix();
	glTranslated(-D - 0.5f, W + 0.5f, 1.0);
	glutSolidSphere(1.2, 50.f, 50.f);
	glPopMatrix();
	
	glPushMatrix();
	glTranslated(D + 0.5, W + 0.5, 1.0);
	glutSolidSphere(1.2, 50.f, 50.f);
	glPopMatrix();
	
	glPushMatrix();
	glTranslated(-D - 0.5, -W - 0.5, 1.0);
	glutSolidSphere(1.2, 50.f, 50.f);
	glPopMatrix();
	
	glPushMatrix();
	glTranslated(D + 0.5, -W - 0.5, 1.0);
	glutSolidSphere(1.2, 50.f, 50.f);
	glPopMatrix();
	
	glPushMatrix();
	glTranslated(0.0, W + 1.10, 1.0);
	glutSolidSphere(1.2, 50.f, 50.f);
	glPopMatrix();

	glPushMatrix();
	glTranslated(0.0, -W - 1.25, 1.0);
	glutSolidSphere(1.2, 50.f, 50.f);
	glPopMatrix();

}

bool collision(float& x_1, float& y_1, float& x_2, float& y_2) {
	float xs = abs(x_1 - x_2);
	float ys = abs(y_1 - y_2);

	float c = sqrtf(pow(xs, 2) + pow(ys, 2));

	if (c <= 2.01) return true;

	return false;
}

bool putInPocket(float& x, float& y) {
	
	if (x <-14.8 and y > 9.2) return true; // Top Left Pocket
	if (x <-14.8 and y <-9.6) return true; // Bottom Left Pocket
	
	if (x > 15.0 and y > 9.6) return true; // Top Right Pocket
	if (x > 15.0 and y <-9.6) return true; // Bottom Right Pocket

	if (x > -0.8 and x < 0.6 and y > 10.0) return true; // Top Middle Pocket
	if (x > -0.8 and x < 0.6 and y <-10.0) return true; // Bottom Middle Pocket

	return false;
}

void display() {

	static bool touch = false, before_touch = false;

	glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);
	glLoadIdentity();

	glLightfv(GL_LIGHT0, GL_POSITION, lightPos);

	glPushMatrix(); // Draw Table
		glTranslated(-1.0, 0.0, 0.0);
		glRotated(90.0, 1.0, 0.0, 0.0);
		glMaterialfv(GL_FRONT_AND_BACK, GL_DIFFUSE, table_color);
		myGround(0.0);
	glPopMatrix();

	pockets(); // Pockets

	glPushMatrix(); // red ball
		glTranslated(red_ball->x_pos, red_ball->y_pos, 0.0);
		glMaterialfv(GL_FRONT_AND_BACK, GL_DIFFUSE, red);
		glutSolidSphere(red_ball->radius, 50.f, 50.f);
	glPopMatrix();


	glPushMatrix(); // white ball
		glTranslated(white_ball->x_pos, white_ball->y_pos, 0.0);
		glMaterialfv(GL_FRONT_AND_BACK, GL_DIFFUSE, white);
		glutSolidSphere(white_ball->radius, 50.f, 50.f);
	glPopMatrix();

	touch = collision(white_ball->x_pos, white_ball->y_pos, red_ball->x_pos, red_ball->y_pos);
	if (!before_touch and touch) {
		glutTimerFunc(0, timer, 2);
	}
	before_touch = touch;

	cout << "-----------------------------------" << endl << endl;
	cout << "acceleration: " << accel << endl;
	cout << "Red Ball X Pos: " << red_ball->x_pos << endl;
	cout << "Red Ball Y Pos: " << red_ball->y_pos << endl;
	cout << "Red Ball Acc: " << red_ball->accel << endl;
	cout << "White Ball Acc: " << white_ball->accel << endl;
	cout << "-----------------------------------" << endl << endl;

	glutSwapBuffers();
}

void idle() {
	glutPostRedisplay();
}

void timer (int state) {

	switch (state)
	{
	case 0: // Loop Until Balls move.
		if (red_ball->swt) {
			ball_motion(red_ball, 1);
		}
		if (white_ball->swt) {
			ball_motion(white_ball, 2);
		}
		if (red_ball->swt or white_ball->swt) glutTimerFunc(1000 / 60, timer, 0);
		break;

	case 1: { // Start Red Ball
		red_ball->swt = true;
		red_ball->accel = accel;
		glutTimerFunc(1000 / 60, timer, 0);
	} break;

	case 2: { // Start White Ball
		white_ball->direction_x = white_ball->x_pos - red_ball->x_pos > 0 ? 1 : -1;
		white_ball->direction_y = white_ball->y_pos - red_ball->y_pos > 0 ? 1 : -1;

		white_ball->accel = red_ball->accel;
		red_ball->accel -= red_ball->accel * 0.2;

		white_ball->swt = true;
	} break;

	case 3: {
		// RESET
		red_ball = new BallObject(radius, 0.0, 0.0, 1.0, 0.0, 0.0, 1, 1, false);
		white_ball = new BallObject(radius, 5.0, 5.0, 1.0, 0.0, 0.0, 1, 1, false);
		glutIdleFunc(0);
	}

	}
	
}

void ball_motion(BallObject* obj, int type) { // Ball movement function

	if (putInPocket(obj->x_pos, obj->y_pos)) {
		obj->accel == 0.0;
		obj->swt = false;
		obj->radius = 0;
		glutIdleFunc(0);
	}

	if (obj->accel <= 0.0) {
		obj->swt = false;
		glutIdleFunc(0);
		obj->accel = 0.0;
		cout << &obj->name << endl;
	}

	if (obj->swt) {
		glutIdleFunc(idle);

		// For X direction
		switch (obj->direction_x)
		{
		case 1:
			if (obj->x_pos < D) obj->x_pos += Vx * obj->accel;
			else {
				obj->accel -= obj->accel * 0.07;
				obj->direction_x = obj->direction_x * (-1);
			}
			break;
		case -1:
			if (obj->x_pos > -D) obj->x_pos -= Vx * obj->accel;
			else {
				obj->accel -= obj->accel * 0.07;
				obj->direction_x = obj->direction_x * (-1);
			}
			break;
		}

		// For Z direction
		switch (obj->direction_y)
		{
		case 1:
			if (obj->y_pos < W) obj->y_pos += Vy * obj->accel;
			else {
				obj->accel -= obj->accel * 0.07;
				obj->direction_y = obj->direction_y * (-1);
			}
			break;
		case -1:
			if (obj->y_pos > -W) obj->y_pos -= Vy * obj->accel;
			else {
				obj->accel -= obj->accel * 0.07;
				obj->direction_y = obj->direction_y * (-1);
			}
			break;
		}
		obj->accel -= 0.002; // Negative Acceleration
	}
}

void mouse(int button, int state, int x, int y) {
	switch (button)
	{
	case GLUT_LEFT_BUTTON:
		if (state == GLUT_DOWN) {
			x_0 = x;
			y_0 = y;
		}
		else if (state == GLUT_UP) {
			glutTimerFunc(0, timer, 1);
		}
		break;
	case GLUT_RIGHT_BUTTON:

		if (state == GLUT_DOWN) {
			glutTimerFunc(0, timer, 3);
		}
		break;
	}
}

void dragMotion(int x, int y) {

	x_1 = x;
	y_1 = y;

	int d_x = abs(x_0 - x_1);
	int d_y = abs(y_0 - y_1);

	hypothesis = sqrtf(pow(d_x, 2) + pow(d_y, 2));

	direction_x = (x_0 - x_1) > 0 ? 1 : -1;
	direction_y = (y_0 - y_1) > 0 ? -1 : 1;

	red_ball->direction_x = direction_x;
	red_ball->direction_y = direction_y;

	float V0 = min(1.2f, hypothesis / 70);

	Vx = V0 * (d_x / hypothesis);
	Vy = V0 * (d_y / hypothesis);

	accel = max(Vx, Vy);
}
void passiveMotion(int x, int y) {
	// cout << "Passive Motion:  x - " << x << ", y - " << y << endl;
}

void keyboard(unsigned char key, int x, int y) {
	if (key == '\033' or key == 'q' or key == 'Q') exit(0);
}
void resize(int w, int h) {

	glViewport(0, 0, (GLsizei)w, (GLsizei)h);
	glMatrixMode(GL_PROJECTION);

	glLoadIdentity();
	gluOrtho2D(-20.0, 20.0, -15.0, 15.0);
	// gluPerspective(30.0, (double) w / h, 0.1, 200.0);

	glMatrixMode(GL_MODELVIEW);

}

void init() {
	glClearColor(0.9, 0.9, 0.9, 1.0);
	glClearDepth(1.f);
	glEnable(GL_DEPTH_TEST);

	glLightfv(GL_LIGHT0, GL_AMBIENT, lightAmb);
	glLightfv(GL_LIGHT0, GL_DIFFUSE, lightDiff);

	glEnable(GL_LIGHT0);
	glEnable(GL_LIGHTING);
}