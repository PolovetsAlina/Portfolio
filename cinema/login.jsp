<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.sql.*" %>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login Processing</title>
</head>
<body>
<%
    String username = request.getParameter("username");
    String password = request.getParameter("password");
    
    Connection conn = null;
    PreparedStatement pstmt = null;
    ResultSet rs = null;
    
    try {
        Class.forName("com.mysql.cj.jdbc.Driver");
        conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
        
        String sql = "SELECT * FROM utenti WHERE Username = ? AND Password = ?";
        pstmt = conn.prepareStatement(sql);
        pstmt.setString(1, username);
        pstmt.setString(2, password);
        
        rs = pstmt.executeQuery();
        
        if (rs.next()) {
            session.setAttribute("username", username);
            session.setAttribute("role", rs.getString("Ruolo"));

            String role = rs.getString("Ruolo");
            if ("admin".equals(role)) {
                response.sendRedirect("area_amministratore.jsp?username=" + username);
            } else {
                response.sendRedirect("area_riservata.jsp?username=" + username);
            }
        } else {
            response.sendRedirect("index.jsp?error=invalid");
        }
        
    } catch (Exception e) {
        out.println("Error: " + e.getMessage());
    } finally {
        try { 
            if (rs != null) rs.close(); 
        } catch (Exception e) { }
        try { 
            if (pstmt != null) pstmt.close(); 
        } catch (Exception e) { }
        try { 
            if (conn != null) conn.close(); 
        } catch (Exception e) { }
    }
%>
</body>
</html>
