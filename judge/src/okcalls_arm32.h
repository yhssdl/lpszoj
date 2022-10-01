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
int LANG_CV[256] = { 0,85, 8,140, 218, 252,262,272,302,318,334, 0 };
//java
int LANG_JV[256] = { 0,218,262,272,295,302,318,334,435,  0 };
//python
int LANG_YV[256]={0,3,4,5,6,11,33,45,54,85,116,122,125,140,174,175,183,
		191,192,195,196,197,199,200,201,202,218,220,243,252,258,262,272,
		311,13,41,91,102,186,221,240,295,302,318,334,0};
//php
int LANG_PHV[256] = {0,3,4,5,6,11,13,33,45,54,78,91,122,125,140,174,175,183,191,192,195,
		     196,197,218,240,243,252,258,262,272,295,311,146, 158, 117, 60, 39, 102, 302,318,334,0 };
		
struct ok_call {
	int * call;
};
struct ok_call ok_calls[] = {
	{LANG_CV},
	{LANG_CV},
	{LANG_JV},
	{LANG_YV}
};