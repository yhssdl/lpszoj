/*
 * 
 *
 * This file is part of HUSTOJ.
 *
 * HUSTOJ is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HUSTOJ is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HUSTOJ. if not, see <http://www.gnu.org/licenses/>.
 */
//c & c++
int LANG_CV[256] = {0,1,2,3,4,5,8,9,10,11,12,20,21,59,63,89,99,158,202,231,240,272,273,275,511, 0 };
//java
int LANG_JV[256] = {0,2,3,4,5,9,10,11,12,13,14,17,21,56,59,89,97,104,157,158,202,218,231,272,273,257,
		61, 22, 6, 33, 8, 13, 16, 111, 110, 39, 79, 302,  0 };
//python
int LANG_YV[256] = {0,2,3,4,5,6,8,9,10,11,12,13,14,16,17,21,32,59,72,78,79,89,97,99,102,104,107,108,131,158,217,218,228,231,272,273,318,39,99,302,99,32,72,131,1,202,257,41, 42, 146, 158, 117, 60, 39, 102, 191, 0 };

struct ok_call {
	int * call;
};
struct ok_call ok_calls[] = {
	{LANG_CV},
	{LANG_CV},
	{LANG_JV},
	{LANG_YV}
};